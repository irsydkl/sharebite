<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['claim_id', 'user_id', 'amount', 'service_fee', 'donor_amount', 'payment_method', 'payment_status', 'transaction_reference', 'paid_at'];

    protected $casts = ['paid_at' => 'datetime'];

    public function claim()
    {
        return $this->belongsTo(FoodClaim::class, 'claim_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payout()
    {
        return $this->hasOne(Payout::class);
    }
}
