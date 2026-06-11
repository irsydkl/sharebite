<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use App\Models\DonorProfile;
use App\Models\Food;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalDonaturs = User::where('role', 'donatur')->count();
        $totalFoods = Food::count();
        $pendingDonatursCount = DonorProfile::where('approval_status', 'pending')->count();
        $pendingFoodsCount = Food::where('approval_status', 'pending')->count();

        $totalPayouts = Payout::sum('amount');
        $totalPayments = Payment::where('payment_status', 'paid')->sum('amount');

        return view('Pages.Admin.dashboard', compact(
            'totalUsers',
            'totalDonaturs',
            'totalFoods',
            'pendingDonatursCount',
            'pendingFoodsCount',
            'totalPayouts',
            'totalPayments'
        ));
    }

    /**
     * List pending donaturs.
     */
    public function pendingDonaturs()
    {
        $donaturs = DonorProfile::where('approval_status', 'pending')
            ->with(['user', 'locationProofs'])
            ->latest()
            ->get();

        return view('Pages.Admin.donatur-verifikasi', compact('donaturs'));
    }

    /**
     * Process donatur approval.
     */
    public function approveDonatur(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                $donorProfile = DonorProfile::lockForUpdate()->findOrFail($id);

                if ($donorProfile->approval_status !== 'pending') {
                    throw new \Exception('Profil donatur sudah diproses.');
                }

                $status = $request->input('status');
                $notes = $request->input('notes');

                // Log approval
                ApprovalLog::create([
                    'admin_id' => Auth::id(),
                    'donor_profile_id' => $donorProfile->id,
                    'status' => $status,
                    'notes' => $notes,
                ]);

                // Update donor profile
                $donorProfile->approval_status = $status;
                if ($status === 'approved') {
                    $donorProfile->is_verified = true;
                    $donorProfile->location_verified = true;
                }
                $donorProfile->verified_by = Auth::id();
                $donorProfile->verified_at = now();
                $donorProfile->save();

                // Update associated user
                $user = $donorProfile->user;
                if ($status === 'approved') {
                    $user->is_verified = true;
                    $user->verified_at = now();
                }
                $user->save();

                // Create notification for donatur
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $status === 'approved' ? 'Pendaftaran Donatur Disetujui' : 'Pendaftaran Donatur Ditolak',
                    'message' => $status === 'approved'
                        ? 'Akun Donatur Anda telah disetujui! Anda sekarang dapat mengunggah makanan.'
                        : 'Maaf, pendaftaran donatur Anda ditolak karena: '.($notes ?? 'Dokumen tidak lengkap.'),
                    'type' => $status === 'approved' ? 'success' : 'error',
                ]);
            });

            return redirect()->route('admin.verifikasi.donatur')
                ->with('success', 'Status pendaftaran donatur berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * List pending foods.
     */
    public function pendingFoods()
    {
        $foods = Food::where('approval_status', 'pending')
            ->with(['donor.donorProfile', 'category', 'images'])
            ->latest()
            ->get();

        return view('Pages.Admin.makanan-verifikasi', compact('foods'));
    }

    /**
     * Process food approval.
     */
    public function approveFood(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                $food = Food::lockForUpdate()->findOrFail($id);

                if ($food->approval_status !== 'pending') {
                    throw new \Exception('Makanan sudah diproses.');
                }

                $status = $request->input('status');
                $notes = $request->input('notes');

                // Log approval
                ApprovalLog::create([
                    'admin_id' => Auth::id(),
                    'food_id' => $food->id,
                    'status' => $status,
                    'notes' => $notes,
                ]);

                // Update food
                $food->approval_status = $status;
                $food->approved_by = Auth::id();
                $food->save();

                // Create notification for donatur
                Notification::create([
                    'user_id' => $food->donor_id,
                    'title' => $status === 'approved' ? 'Makanan Disetujui' : 'Makanan Ditolak',
                    'message' => $status === 'approved'
                        ? "Makanan '{$food->title}' Anda telah disetujui dan sekarang tersedia untuk diklaim."
                        : "Maaf, makanan '{$food->title}' ditolak karena: ".($notes ?? 'Deskripsi tidak sesuai.'),
                    'type' => $status === 'approved' ? 'success' : 'error',
                ]);
            });

            return redirect()->route('admin.verifikasi.food')
                ->with('success', 'Status makanan berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display payouts page.
     */
    public function payouts()
    {
        $payments = Payment::where('payment_status', 'paid')
            ->with(['claim.food.donor.donorProfile', 'payout'])
            ->latest()
            ->get();

        $payouts = Payout::with(['payment.claim.food', 'donor.donorProfile'])
            ->latest()
            ->get();

        return view('Pages.Admin.payouts', compact('payments', 'payouts'));
    }

    /**
     * Process donor payout — delegates to PaymentService.
     */
    public function processPayout(Request $request, $id)
    {
        try {
            $payout = Payout::findOrFail($id);
            $this->paymentService->processPayout($payout);

            return redirect()->route('admin.payouts')
                ->with('success', 'Payout berhasil diproses dan dana telah ditransfer ke saldo donatur.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
