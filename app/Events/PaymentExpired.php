<?php

namespace App\Events;

use App\Models\FoodClaim;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentExpired
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly FoodClaim $claim) {}
}
