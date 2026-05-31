<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'food_id', 'donor_profile_id', 'status', 'notes'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function donorProfile()
    {
        return $this->belongsTo(DonorProfile::class);
    }
}
