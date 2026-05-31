<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodClaim extends Model
{
    use HasFactory;

    protected $fillable = ['food_id', 'user_id', 'quantity_claimed', 'subtotal_price', 'service_fee', 'total_price', 'booking_code', 'payment_expired_at', 'pickup_deadline', 'picked_up_at', 'pickup_proof', 'claim_status'];

    protected $casts = ['payment_expired_at' => 'datetime', 'pickup_deadline' => 'datetime', 'picked_up_at' => 'datetime'];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'claim_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'claim_id');
    }
}
