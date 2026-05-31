<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = ['donor_id', 'category_id', 'approved_by', 'title', 'description', 'quantity', 'remaining_quantity', 'unit', 'original_price', 'service_fee', 'final_price', 'pickup_address', 'latitude', 'longitude', 'pickup_start', 'pickup_end', 'pickup_deadline', 'pickup_duration_minutes', 'approval_status', 'status', 'expired_at'];

    protected $casts = ['pickup_start' => 'datetime', 'pickup_end' => 'datetime', 'pickup_deadline' => 'datetime', 'expired_at' => 'datetime'];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(FoodImage::class);
    }

    public function claims()
    {
        return $this->hasMany(FoodClaim::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
