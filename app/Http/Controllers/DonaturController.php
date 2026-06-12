<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodClaim;
use App\Models\FoodImage;
use App\Models\Notification;
use App\Models\Payout;
use App\Models\ProductCategory;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonaturController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function index()
    {
        $userId = Auth::id();

        $totalUnggahan = Food::where('donor_id', $userId)->count();

        $approved = Food::where('donor_id', $userId)
            ->where('approval_status', 'approved')
            ->count();

        $pending = Food::where('donor_id', $userId)
            ->where('approval_status', 'pending')
            ->count();

        $nightApproved = Food::where('donor_id', $userId)
            ->where('approval_status', 'approved')
            ->where(function ($q) {
                $q->whereTime('pickup_start', '>=', '17:00:00')
                    ->orWhereTime('pickup_end', '>=', '18:00:00');
            })
            ->count();

        $rejected = Food::where('donor_id', $userId)
            ->where('approval_status', 'rejected')
            ->count();

        $totalPorsi = FoodClaim::whereHas('food', fn ($q) => $q->where('donor_id', $userId))
            ->whereIn('claim_status', ['picked_up', 'completed'])
            ->sum('quantity_claimed');

        $foodWaste = $totalPorsi;

        // Fetch history of claimed portions for chart (last 30 days)
        $portionsHistory = FoodClaim::whereHas('food', fn ($q) => $q->where('donor_id', $userId))
            ->whereIn('claim_status', ['picked_up', 'completed'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(quantity_claimed) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return view('Pages.Donatur.dashboard', compact(
            'totalUnggahan',
            'approved',
            'pending',
            'rejected',
            'nightApproved',
            'totalPorsi',
            'foodWaste',
            'portionsHistory'
        ));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        $profile = Auth::user()->donorProfile;

        return view('Pages.Donatur.upload', compact('categories', 'profile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'original_price' => 'required|numeric|min:0',
            'pickup_address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'pickup_start' => 'required|date|after_or_equal:now',
            'pickup_end' => 'required|date|after:pickup_start',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $originalPrice = (float) $request->input('original_price');
                $serviceFee = round($originalPrice * 0.1, 2);
                $finalPrice = $originalPrice + $serviceFee;

                $pickupStart = new \DateTime($request->input('pickup_start'));
                $pickupEnd = new \DateTime($request->input('pickup_end'));

                $pickupDeadline = clone $pickupEnd;
                $pickupDeadline->modify('-30 minutes');

                $food = Food::create([
                    'donor_id' => Auth::id(),
                    'category_id' => $request->input('category_id'),
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'quantity' => $request->input('quantity'),
                    'remaining_quantity' => $request->input('quantity'),
                    'unit' => $request->input('unit'),
                    'original_price' => $originalPrice,
                    'service_fee' => $serviceFee,
                    'final_price' => $finalPrice,
                    'pickup_address' => $request->input('pickup_address'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'pickup_start' => $pickupStart,
                    'pickup_end' => $pickupEnd,
                    'pickup_deadline' => $pickupDeadline,
                    'pickup_duration_minutes' => 60,
                    'approval_status' => 'pending',
                    'status' => 'available',
                ]);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $file) {
                        $path = $file->store('foods', 'public');
                        FoodImage::create([
                            'food_id' => $food->id,
                            'image' => $path,
                        ]);
                    }
                }

                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'Makanan Baru Perlu Verifikasi',
                        'message' => "Makanan '{$food->title}' baru diunggah oleh donatur ".Auth::user()->name.' dan membutuhkan verifikasi.',
                        'type' => 'info',
                    ]);
                }
            });

            return redirect()->route('donatur.dashboard')
                ->with('success', 'Makanan berhasil diunggah dan sedang menunggu persetujuan admin.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function claims()
    {
        $claims = FoodClaim::whereHas('food', fn ($q) => $q->where('donor_id', Auth::id()))
            ->with(['food', 'user', 'payment'])
            ->latest()
            ->get();

        return view('Pages.Donatur.riwayat', compact('claims'));
    }

    public function updateClaimStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ready_pickup,picked_up,completed,cancelled',
            'pickup_proof' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $newStatus = $request->input('status');

            if ($newStatus === 'picked_up') {
                $claim = FoodClaim::where('id', $id)
                    ->whereHas('food', fn ($q) => $q->where('donor_id', Auth::id()))
                    ->firstOrFail();

                if ($request->hasFile('pickup_proof')) {
                    $path = $request->file('pickup_proof')->store('pickup_proofs', 'public');
                    $claim->pickup_proof = $path;
                    $claim->save();
                }

                $this->paymentService->completePickup($claim);
                $claim->refresh();

                Notification::create([
                    'user_id' => $claim->user_id,
                    'title' => 'Makanan Telah Diambil',
                    'message' => "Makanan untuk booking {$claim->booking_code} telah dinyatakan selesai diambil. Silakan berikan ulasan Anda.",
                    'type' => 'success',
                ]);

                return redirect()->route('donatur.claims')
                    ->with('success', 'Pickup berhasil dikonfirmasi. Dana escrow masuk ke saldo pending donatur.');

            }

            DB::transaction(function () use ($id, $newStatus) {
                $claim = FoodClaim::where('id', $id)
                    ->whereHas('food', fn ($q) => $q->where('donor_id', Auth::id()))
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($newStatus === 'cancelled') {
                    if ($claim->claim_status === 'waiting_payment') {
                        $food = $claim->food()->lockForUpdate()->first();
                        $food->remaining_quantity += $claim->quantity_claimed;
                        if ($food->remaining_quantity > 0) {
                            $food->status = 'available';
                        }
                        $food->save();

                        $payment = $claim->payment;
                        if ($payment && $payment->payment_status === 'pending') {
                            $payment->payment_status = 'failed';
                            $payment->save();
                        }
                    }

                    $claim->claim_status = 'cancelled';
                    $claim->save();

                    Notification::create([
                        'user_id' => $claim->user_id,
                        'title' => 'Klaim Dibatalkan',
                        'message' => "Klaim untuk booking {$claim->booking_code} telah dibatalkan oleh Donatur.",
                        'type' => 'warning',
                    ]);
                } else {
                    $claim->claim_status = $newStatus;
                    $claim->save();
                }
            });

            return redirect()->route('donatur.claims')
                ->with('success', 'Status klaim berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payouts()
    {
        $payouts = Payout::where('donor_id', Auth::id())
            ->with(['payment.claim.food'])
            ->latest()
            ->get();

        return view('Pages.Donatur.payouts', compact('payouts'));
    }
}
