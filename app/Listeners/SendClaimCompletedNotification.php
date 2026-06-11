<?php

namespace App\Listeners;

use App\Events\ClaimCompleted;
use App\Models\Notification;

class SendClaimCompletedNotification
{
    public function handle(ClaimCompleted $event): void
    {
        $claim = $event->claim;
        $claim->load(['food', 'food.donor']);

        // Notify user (penerima)
        Notification::create([
            'user_id' => $claim->user_id,
            'title'   => 'Pengambilan Selesai',
            'message' => "Pengambilan makanan untuk booking {$claim->booking_code} telah selesai. Berikan ulasan Anda!",
            'type'    => 'success',
        ]);

        // Notify donatur
        if ($claim->food) {
            Notification::create([
                'user_id' => $claim->food->donor_id,
                'title'   => 'Transaksi Selesai',
                'message' => "Pengambilan '{$claim->food->title}' kode {$claim->booking_code} telah selesai. Dana Anda sedang diproses untuk pencairan.",
                'type'    => 'success',
            ]);
        }
    }
}
