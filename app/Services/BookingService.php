<?php

namespace App\Services;

use App\Models\FoodClaim;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    // ------------------------------------------------------------------ //
    // Create a claim + payment record + Midtrans token in one transaction
    // ------------------------------------------------------------------ //
    public function book(int $foodId, int $userId, int $quantityClaimed, PaymentService $paymentService): array
    {
        return DB::transaction(function () use ($foodId, $userId, $quantityClaimed, $paymentService) {
            $food = \App\Models\Food::where('id', $foodId)->lockForUpdate()->firstOrFail();

            // Validations
            if ($food->status !== 'available' || $food->approval_status !== 'approved') {
                throw new \Exception('Makanan sudah tidak tersedia.');
            }

            if ($food->remaining_quantity < $quantityClaimed) {
                throw new \Exception('Stok makanan tidak mencukupi. Tersisa: '.$food->remaining_quantity.' '.$food->unit);
            }

            // Deduct stock
            $food->remaining_quantity -= $quantityClaimed;
            if ($food->remaining_quantity === 0) {
                $food->status = 'claimed';
            }
            $food->save();

            // Price calculation (per-unit)
            $unitOriginal = $food->original_price / max($food->quantity, 1);
            $unitFee      = $food->service_fee / max($food->quantity, 1);
            $subtotal     = round($unitOriginal * $quantityClaimed, 2);
            $serviceFee   = round($unitFee * $quantityClaimed, 2);
            $total        = $subtotal + $serviceFee;

            $bookingCode = strtoupper('BK-'.Str::random(8));

            // Create food claim (escrow gate: waiting_payment)
            $claim = FoodClaim::create([
                'food_id'           => $food->id,
                'user_id'           => $userId,
                'quantity_claimed'  => $quantityClaimed,
                'subtotal_price'    => $subtotal,
                'service_fee'       => $serviceFee,
                'total_price'       => $total,
                'booking_code'      => $bookingCode,
                'payment_expired_at' => now()->addMinutes(5),
                'pickup_deadline'   => $food->pickup_deadline,
                'claim_status'      => 'waiting_payment',
            ]);

            // Create pending payment record
            $payment = Payment::create([
                'claim_id'       => $claim->id,
                'user_id'        => $userId,
                'amount'         => $total,
                'service_fee'    => $serviceFee,
                'donor_amount'   => $subtotal,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
            ]);

            // Get Midtrans Snap token
            $snapToken = $paymentService->createSnapToken($claim);

            // Notify user
            Notification::create([
                'user_id' => $userId,
                'title'   => 'Klaim Berhasil Dibuat',
                'message' => "Klaim untuk {$food->title} sebanyak {$quantityClaimed} {$food->unit} berhasil dibuat. Selesaikan pembayaran dalam 5 menit.",
                'type'    => 'info',
            ]);

            // Notify donatur
            $user = \App\Models\User::find($userId);
            Notification::create([
                'user_id' => $food->donor_id,
                'title'   => 'Makanan Diklaim',
                'message' => "Makanan Anda '{$food->title}' telah diklaim sebanyak {$quantityClaimed} {$food->unit} oleh {$user->name}.",
                'type'    => 'info',
            ]);

            return [
                'claim'      => $claim,
                'payment'    => $payment,
                'snap_token' => $snapToken,
            ];
        });
    }
}
