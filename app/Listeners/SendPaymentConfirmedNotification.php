<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Models\Notification;

class SendPaymentConfirmedNotification
{
    public function handle(PaymentConfirmed $event): void
    {
        $claim = $event->claim;
        $claim->load(['food', 'food.donor']);

        // Notify user (penerima)
        Notification::create([
            'user_id' => $claim->user_id,
            'title'   => 'Pembayaran Berhasil',
            'message' => "Pembayaran untuk booking {$claim->booking_code} telah dikonfirmasi. Silakan ambil makanan Anda sebelum ".
                         optional($claim->pickup_deadline)->format('d M Y H:i').'.',
            'type'    => 'success',
        ]);

        // Notify donatur
        if ($claim->food) {
            Notification::create([
                'user_id' => $claim->food->donor_id,
                'title'   => 'Pembayaran Diterima',
                'message' => "Pembayaran untuk '{$claim->food->title}' kode {$claim->booking_code} telah dikonfirmasi. Dana akan dicairkan setelah pengambilan selesai.",
                'type'    => 'success',
            ]);
        }
    }
}
