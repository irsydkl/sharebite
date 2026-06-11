<?php

namespace App\Services;

use App\Events\ClaimCompleted;
use App\Events\PaymentConfirmed;
use App\Events\PaymentExpired;
use App\Models\FoodClaim;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    // ------------------------------------------------------------------ //
    // 1. Create Midtrans Snap token for a pending claim
    // ------------------------------------------------------------------ //
    public function createSnapToken(FoodClaim $claim): string
    {
        $claim->load(['food', 'user']);

        $foodPrice = (int) round($claim->subtotal_price);
        $serviceFeePrice = (int) round($claim->service_fee);
        $grossAmount = $foodPrice + $serviceFeePrice;

        $params = [
            'transaction_details' => [
                'order_id' => $claim->booking_code . '-' . time(),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $claim->user->name,
                'email'      => $claim->user->email,
                'phone'      => $claim->user->phone ?? '',
            ],
            'item_details' => [
                [
                    'id'       => 'food-'.$claim->food_id,
                    'price'    => $foodPrice,
                    'quantity' => 1,
                    'name'     => substr($claim->food->title, 0, 40) . ' (' . $claim->quantity_claimed . ' ' . $claim->food->unit . ')',
                ],
                [
                    'id'       => 'service-fee',
                    'price'    => $serviceFeePrice,
                    'quantity' => 1,
                    'name'     => 'Biaya Layanan',
                ],
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minutes',
                'duration'   => 5,
            ],
            'enabled_payments' => [
                'qris',
                'dana',
                'ovo',
                'linkaja',
                'gopay',
                'shopeepay',
                'bank_transfer',
            ],
        ];

        try {
            $serverKey = config('midtrans.server_key');
            if (empty($serverKey) || str_contains($serverKey, 'XXXX')) {
                throw new \Exception('Midtrans Server Key is empty or placeholder');
            }
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::warning('Midtrans Snap API failed: ' . $e->getMessage() . '. Falling back to mock token.');
            return 'mock-snap-token-' . $claim->booking_code;
        }
    }

    // ------------------------------------------------------------------ //
    // 2. Handle Midtrans webhook notification
    // ------------------------------------------------------------------ //
    public function handleWebhook(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        $status  = $payload['transaction_status'] ?? null;

        if (! $orderId) {
            return;
        }

        DB::transaction(function () use ($orderId, $status, $payload) {
            $bookingCode = explode('-', $orderId)[0];
            $claim = FoodClaim::where('booking_code', $bookingCode)
                ->lockForUpdate()
                ->first();

            if (! $claim) {
                Log::warning("Midtrans webhook: claim not found for order_id={$orderId}");
                return;
            }

            $payment = Payment::where('claim_id', $claim->id)->lockForUpdate()->first();

            if (in_array($status, ['capture', 'settlement'])) {
                $this->markPaymentPaid($claim, $payment, $payload);
            } elseif (in_array($status, ['cancel', 'deny', 'expire'])) {
                $this->markPaymentFailed($claim, $payment, $payload);
            } elseif ($status === 'pending') {
                // Nothing yet — Midtrans still processing
            }
        });
    }

    private function markPaymentPaid(FoodClaim $claim, ?Payment $payment, array $payload): void
    {
        if ($claim->claim_status !== 'waiting_payment') {
            return; // already processed
        }

        // Update payment record
        if ($payment) {
            $payment->payment_status        = 'paid';
            $payment->paid_at               = now();
            $payment->transaction_reference = $payload['transaction_id'] ?? null;
            $payment->payment_method        = $payload['payment_type'] ?? $payment->payment_method;
            $payment->save();
        }

        // Update claim — move to ready_pickup (escrow)
        $claim->claim_status = 'ready_pickup';
        $claim->save();

        event(new PaymentConfirmed($claim));
    }

    private function markPaymentFailed(FoodClaim $claim, ?Payment $payment, array $payload): void
    {
        if (! in_array($claim->claim_status, ['waiting_payment'])) {
            return;
        }

        if ($payment) {
            $payment->payment_status = 'failed';
            $payment->save();
        }

        event(new PaymentExpired($claim));
    }

    // ------------------------------------------------------------------ //
    // 3. Complete pickup — release escrow to donatur (pending payout)
    // ------------------------------------------------------------------ //
    public function completePickup(FoodClaim $claim): void
    {
        DB::transaction(function () use ($claim) {
            $claim = FoodClaim::where('id', $claim->id)->lockForUpdate()->firstOrFail();

            if ($claim->claim_status !== 'ready_pickup') {
                throw new \Exception('Status klaim tidak valid untuk penyelesaian.');
            }

            $claim->claim_status = 'completed';
            $claim->picked_up_at = now();
            $claim->save();

            // Load payment (escrow)
            $payment = $claim->payment;
            if (! $payment || $payment->payment_status !== 'paid') {
                throw new \Exception('Pembayaran tidak ditemukan atau belum lunas.');
            }

            // Create pending payout (donor balance updated when admin processes)
            $existsPayout = Payout::where('payment_id', $payment->id)->exists();
            if (! $existsPayout) {
                Payout::create([
                    'payment_id' => $payment->id,
                    'donor_id'   => $claim->food->donor_id,
                    'amount'     => $payment->donor_amount,
                    'status'     => 'pending',
                    'sent_at'    => null,
                ]);
            }

            event(new ClaimCompleted($claim));
        });
    }

    // ------------------------------------------------------------------ //
    // 4. Admin processes payout — release escrow to donatur balance
    // ------------------------------------------------------------------ //
    public function processPayout(Payout $payout): void
    {
        DB::transaction(function () use ($payout) {
            $payout = Payout::where('id', $payout->id)->lockForUpdate()->firstOrFail();

            if ($payout->status !== 'pending') {
                throw new \Exception('Payout sudah diproses sebelumnya.');
            }

            $payout->status  = 'completed';
            $payout->sent_at = now();
            $payout->save();

            // Credit donatur balance (escrow release)
            $donor = User::lockForUpdate()->findOrFail($payout->donor_id);
            $donor->balance += $payout->amount;
            $donor->save();

            // Notify donatur
            Notification::create([
                'user_id' => $payout->donor_id,
                'title'   => 'Pencairan Dana Berhasil',
                'message' => 'Dana sebesar Rp '.number_format($payout->amount, 0, ',', '.')
                    ." dari kode booking {$payout->payment->claim->booking_code} telah ditambahkan ke saldo Anda.",
                'type'    => 'success',
            ]);
        });
    }

    // ------------------------------------------------------------------ //
    // 5. Expire overdue claims (scheduler)
    // ------------------------------------------------------------------ //
    public function expireOverdueClaims(): int
    {
        $expired = FoodClaim::where('claim_status', 'waiting_payment')
            ->where('payment_expired_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $claim) {
            try {
                DB::transaction(function () use ($claim) {
                    $claim = FoodClaim::where('id', $claim->id)->lockForUpdate()->firstOrFail();

                    if ($claim->claim_status !== 'waiting_payment') {
                        return;
                    }

                    // Restore stock
                    $food = $claim->food()->lockForUpdate()->first();
                    if ($food) {
                        $food->remaining_quantity += $claim->quantity_claimed;
                        if ($food->status === 'claimed' && $food->remaining_quantity > 0) {
                            $food->status = 'available';
                        }
                        $food->save();
                    }

                    // Cancel claim + fail pending payment
                    $claim->claim_status = 'cancelled';
                    $claim->save();

                    $payment = $claim->payment;
                    if ($payment && $payment->payment_status === 'pending') {
                        $payment->payment_status = 'failed';
                        $payment->save();
                    }

                    event(new PaymentExpired($claim));
                });
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to expire claim #{$claim->id}: ".$e->getMessage());
            }
        }

        return $count;
    }

    // ------------------------------------------------------------------ //
    // 6. Auto-complete claims past pickup_deadline (scheduler)
    // ------------------------------------------------------------------ //
    public function autoCompleteOverduePickups(): int
    {
        $overdue = FoodClaim::where('claim_status', 'ready_pickup')
            ->where('pickup_deadline', '<', now())
            ->get();

        $count = 0;
        foreach ($overdue as $claim) {
            try {
                $this->completePickup($claim);
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-complete claim #{$claim->id}: ".$e->getMessage());
            }
        }

        return $count;
    }
}
