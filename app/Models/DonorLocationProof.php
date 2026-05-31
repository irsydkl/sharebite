<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorLocationProof extends Model
{
    use HasFactory;

    protected $fillable = ['donor_profile_id', 'image', 'notes'];

    public function donorProfile()
    {
        return $this->belongsTo(DonorProfile::class);
    }
}
