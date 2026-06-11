<?php

namespace App\Listeners;

use App\Events\PaymentExpired;
use App\Models\Notification;

class SendPaymentExpiredNotification
{
    public function handle(PaymentExpired $event): void
    {
        $claim = $event->claim;

        Notification::create([
            'user_id' => $claim->user_id,
            'title'   => 'Pembayaran Kedaluwarsa',
            'message' => "Klaim untuk booking {$claim->booking_code} dibatalkan karena pembayaran tidak diselesaikan dalam waktu yang ditentukan. Stok telah dikembalikan.",
            'type'    => 'error',
        ]);
    }
}
