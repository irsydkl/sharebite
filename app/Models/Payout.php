<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = ['payment_id', 'donor_id', 'amount', 'status', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }
}
