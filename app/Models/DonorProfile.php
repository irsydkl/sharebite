<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'store_name', 'store_description', 'store_image', 'store_address', 'latitude', 'longitude', 'is_verified', 'location_verified', 'verified_by', 'verified_at', 'approval_status'];

    protected $casts = ['verified_at' => 'datetime', 'is_verified' => 'boolean', 'location_verified' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function locationProofs()
    {
        return $this->hasMany(DonorLocationProof::class);
    }
}
