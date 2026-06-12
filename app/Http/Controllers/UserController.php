<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Food;
use App\Models\FoodClaim;
use App\Models\Notification;
use App\Models\Rating;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly PaymentService $paymentService,
    ) {}

    // ------------------------------------------------------------------ //
    // Dashboard — browse available foods
    // ------------------------------------------------------------------ //
    public function index()
    {
        $foods = Food::where('status', 'available')
            ->where('approval_status', 'approved')
            ->with(['category', 'images', 'donor.donorProfile'])
            ->latest()
            ->get();

        return view('Pages.User.dashboard', compact('foods'));
    }

    // ------------------------------------------------------------------ //
    // Food detail
    // ------------------------------------------------------------------ //
    public function showFood($id)
    {
        $food = Food::with(['category', 'images', 'donor.donorProfile', 'ratings.user'])
            ->findOrFail($id);

        return view('Pages.User.detail', compact('food'));
    }

    // ------------------------------------------------------------------ //
    // Book / Claim food — delegates to BookingService
    // ------------------------------------------------------------------ //
    public function claimFood(BookingRequest $request, $id)
    {
        try {
            $result = $this->bookingService->book(
                foodId: (int) $id,
                userId: Auth::id(),
                quantityClaimed: (int) $request->quantity_claimed,
                paymentService: $this->paymentService,
            );

            return redirect()
                ->route('user.claims.payment', $result['claim']->id)
                ->with('snap_token', $result['snap_token'])
                ->with('success', 'Klaim berhasil! Selesaikan pembayaran dalam 5 menit.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ------------------------------------------------------------------ //
    // Payment page — shows Midtrans Snap popup
    // ------------------------------------------------------------------ //
    public function showPayment($id)
    {
        $claim = FoodClaim::with(['food.donor.donorProfile', 'payment'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($claim->claim_status !== 'waiting_payment') {
            return redirect()->route('riwayat.index')
                ->with('info', 'Pembayaran untuk klaim ini sudah diproses.');
        }

        // Re-generate Snap token if needed (page refresh)
        try {
            $snapToken = $this->paymentService->createSnapToken($claim);
        } catch (\Exception $e) {
            $snapToken = null;
        }

        $clientKey = config('midtrans.client_key');
        $isProduction = config('midtrans.is_production');
        $midtransOrderId = $claim->payment?->transaction_reference;

        return view('Pages.User.payment', compact('claim', 'snapToken', 'clientKey', 'isProduction', 'midtransOrderId'));
    }

    // ------------------------------------------------------------------ //
    // Midtrans finish/error/pending redirect (after Snap popup)
    // ------------------------------------------------------------------ //
    public function paymentReturn(Request $request, $id)
    {
        $claim = FoodClaim::where('user_id', Auth::id())->findOrFail($id);

        return match ($claim->claim_status) {
            'ready_pickup' => redirect()->route('user.claims.payment.success', $id),
            'cancelled' => redirect()->route('user.claims.payment.failed', $id),
            default => redirect()->route('user.claims.payment.success', $id)
                ->with('info', 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.'),
        };
    }

    // ------------------------------------------------------------------ //
    // Payment Success Page
    // ------------------------------------------------------------------ //
    public function paymentSuccess($id)
    {
        $claim = FoodClaim::with(['food.donor.donorProfile', 'payment'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('Pages.User.payment-success', compact('claim'));
    }

    // ------------------------------------------------------------------ //
    // Payment Failed Page
    // ------------------------------------------------------------------ //
    public function paymentFailed($id)
    {
        $claim = FoodClaim::with(['food.donor.donorProfile', 'payment'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('Pages.User.payment-failed', compact('claim'));
    }

    // ------------------------------------------------------------------ //
    // Rate & review a completed claim
    // ------------------------------------------------------------------ //
    public function rateClaim(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                $claim = FoodClaim::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                if (! in_array($claim->claim_status, ['completed', 'picked_up'])) {
                    throw new \Exception('Hanya makanan yang telah diambil yang dapat diulas.');
                }

                if (Rating::where('claim_id', $claim->id)->exists()) {
                    throw new \Exception('Anda sudah memberikan ulasan untuk klaim ini.');
                }

                Rating::create([
                    'food_id' => $claim->food_id,
                    'claim_id' => $claim->id,
                    'user_id' => Auth::id(),
                    'rating' => $request->input('rating'),
                    'review' => $request->input('review'),
                ]);

                if ($claim->claim_status === 'picked_up') {
                    $claim->claim_status = 'completed';
                    $claim->save();
                }

                Notification::create([
                    'user_id' => $claim->food->donor_id,
                    'title' => 'Ulasan Baru Diterima',
                    'message' => 'Pengguna memberikan ulasan bintang '.$request->input('rating')." untuk makanan '{$claim->food->title}'.",
                    'type' => 'success',
                ]);
            });

            return redirect()->route('riwayat.index')
                ->with('success', 'Terima kasih atas ulasan Anda!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
