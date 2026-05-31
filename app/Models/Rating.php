<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['food_id', 'claim_id', 'user_id', 'rating', 'review'];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function claim()
    {
        return $this->belongsTo(FoodClaim::class, 'claim_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
