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
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
    }

    public function createSnapToken(FoodClaim $claim): string
    {
        $claim->load(['food', 'user', 'payment']);

        $payment = $claim->payment;
        if (! $payment) {
            throw new \RuntimeException('Data pembayaran tidak ditemukan.');
        }

        $foodPrice = (int) round($claim->subtotal_price);
        $serviceFeePrice = (int) round($claim->service_fee);
        $grossAmount = $foodPrice + $serviceFeePrice;

        $orderId = $claim->booking_code.'-'.time();

        $nameParts = explode(' ', trim($claim->user->name), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $customerDetails = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $claim->user->email,
        ];

        if (! empty($claim->user->phone)) {
            $customerDetails['phone'] = $claim->user->phone;
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => $customerDetails,
            'item_details' => [
                [
                    'id' => 'food-'.$claim->food_id,
                    'price' => $foodPrice,
                    'quantity' => 1,
                    'name' => substr($claim->food->title, 0, 40).' ('.$claim->quantity_claimed.' '.$claim->food->unit.')',
                ],
                [
                    'id' => 'service-fee',
                    'price' => $serviceFeePrice,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan',
                ],
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

        $serverKey = config('midtrans.server_key');
        if (empty($serverKey) || str_contains($serverKey, 'XXXX')) {
            Log::warning('Midtrans Server Key kosong — menggunakan mock token.');

            $payment->transaction_reference = $orderId;
            $payment->save();

            return 'mock-snap-token-'.$claim->booking_code;
        }

        try {
            $token = Snap::getSnapToken($params);

            $payment->transaction_reference = $orderId;
            $payment->save();

            return $token;
        } catch (\Exception $e) {
            Log::warning('Midtrans Snap API failed: '.$e->getMessage().' — fallback mock token.');

            $payment->transaction_reference = $orderId;
            $payment->save();

            return 'mock-snap-token-'.$claim->booking_code;
        }
    }

    public function handleWebhook(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        $status = $payload['transaction_status'] ?? null;

        if (! $orderId) {
            return;
        }

        DB::transaction(function () use ($orderId, $status, $payload) {
            $payment = Payment::where('transaction_reference', $orderId)->lockForUpdate()->first();

            if ($payment) {
                $claim = FoodClaim::where('id', $payment->claim_id)->lockForUpdate()->first();
            } else {
                $bookingCode = $this->resolveBookingCodeFromOrderId($orderId);
                $claim = FoodClaim::where('booking_code', $bookingCode)->lockForUpdate()->first();
                $payment = $claim
                    ? Payment::where('claim_id', $claim->id)->lockForUpdate()->first()
                    : null;
            }

            if (! $claim) {
                Log::warning("Midtrans webhook: claim not found for order_id={$orderId}");

                return;
            }

            if (in_array($status, ['capture', 'settlement'], true)) {
                $this->markPaymentPaid($claim, $payment, $payload);
            } elseif (in_array($status, ['cancel', 'deny', 'expire'], true)) {
                $this->markPaymentFailed($claim, $payment, $payload);
            }
        });
    }

    public function resolveClaimFromOrderId(string $orderId): ?FoodClaim
    {
        $payment = Payment::where('transaction_reference', $orderId)->first();

        if ($payment) {
            return FoodClaim::where('id', $payment->claim_id)->lockForUpdate()->first();
        }

        $bookingCode = $this->resolveBookingCodeFromOrderId($orderId);

        return FoodClaim::where('booking_code', $bookingCode)->lockForUpdate()->first();
    }

    public function resolveBookingCodeFromOrderId(string $orderId): string
    {
        if (preg_match('/^(.+)-(\d{10,})$/', $orderId, $matches)) {
            return $matches[1];
        }

        return $orderId;
    }

    private function markPaymentPaid(FoodClaim $claim, ?Payment $payment, array $payload): void
    {
        if ($claim->claim_status !== 'waiting_payment') {
            return;
        }

        if ($payment) {
            $payment->payment_status = 'paid';
            $payment->paid_at = now();
            $payment->payment_method = $payload['payment_type'] ?? $payment->payment_method;
            $payment->save();
        }

        $claim->claim_status = 'ready_pickup';
        $claim->save();

        event(new PaymentConfirmed($claim));
    }

    private function markPaymentFailed(FoodClaim $claim, ?Payment $payment, array $payload): void
    {
        if ($claim->claim_status !== 'waiting_payment') {
            return;
        }

        $this->restoreStockForClaim($claim);

        $claim->claim_status = 'cancelled';
        $claim->save();

        if ($payment && $payment->payment_status === 'pending') {
            $payment->payment_status = in_array($payload['transaction_status'] ?? '', ['expire'], true)
                ? 'expired'
                : 'failed';
            $payment->save();
        }

        event(new PaymentExpired($claim));
    }

    public function completePickup(FoodClaim $claim): void
    {
        DB::transaction(function () use ($claim) {
            $claim = FoodClaim::where('id', $claim->id)->lockForUpdate()->firstOrFail();

            if ($claim->claim_status !== 'ready_pickup') {
                throw new \RuntimeException('Status klaim tidak valid untuk penyelesaian pickup.');
            }

            $claim->load('food', 'payment');

            $payment = $claim->payment;
            if (! $payment || $payment->payment_status !== 'paid') {
                throw new \RuntimeException('Pembayaran tidak ditemukan atau belum lunas.');
            }

            $claim->claim_status = 'completed';
            $claim->picked_up_at = now();
            $claim->save();

            // Update donor's balance before handling payout creation
            $donor = User::lockForUpdate()->findOrFail($claim->food->donor_id);
            $donor->balance = (float) $donor->balance + (float) $payment->donor_amount;
            $donor->save();

            $payout = Payout::where('payment_id', $payment->id)->lockForUpdate()->first();

            // Create payout if it does not exist
            if (! $payout) {
                Payout::create([
                    'payment_id' => $payment->id,
                    'donor_id' => $claim->food->donor_id,
                    'amount' => $payment->donor_amount,
                    'status' => 'pending',
                    'sent_at' => null,
                ]);
            }

            event(new ClaimCompleted($claim));
        });
    }

    public function processPayout(Payout $payout): void
    {
        DB::transaction(function () use ($payout) {
            $payout = Payout::where('id', $payout->id)->lockForUpdate()->firstOrFail();

            if ($payout->status !== 'pending') {
                throw new \RuntimeException('Payout sudah diproses sebelumnya.');
            }

            $donor = User::lockForUpdate()->findOrFail($payout->donor_id);

            if ((float) $donor->balance < (float) $payout->amount) {
                throw new \RuntimeException('Saldo donatur tidak mencukupi untuk pencairan.');
            }

            $donor->balance = (float) $donor->balance - (float) $payout->amount;
            $donor->save();

            $payout->status = 'completed';
            $payout->sent_at = now();
            $payout->save();

            Notification::create([
                'user_id' => $payout->donor_id,
                'title' => 'Pencairan Dana Berhasil',
                'message' => 'Dana sebesar Rp '.number_format($payout->amount, 0, ',', '.')
                    ." dari kode booking {$payout->payment->claim->booking_code} telah dicairkan ke rekening Anda.",
                'type' => 'success',
            ]);
        });
    }

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

                    $this->restoreStockForClaim($claim);

                    $claim->claim_status = 'cancelled';
                    $claim->save();

                    $payment = $claim->payment;
                    if ($payment && $payment->payment_status === 'pending') {
                        $payment->payment_status = 'expired';
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

    private function restoreStockForClaim(FoodClaim $claim): void
    {
        $food = $claim->food()->lockForUpdate()->first();

        if (! $food) {
            return;
        }

        $food->remaining_quantity += $claim->quantity_claimed;

        if ($food->remaining_quantity > 0 && in_array($food->status, ['claimed', 'expired'], true)) {
            $food->status = 'available';
        }

        $food->save();
    }
}
