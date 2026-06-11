<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payout>
 */
class PayoutFactory extends Factory
{
    protected $model = Payout::class;

    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory()->paid(),
            'donor_id' => User::factory()->donatur(),
            'amount' => fake()->randomFloat(2, 20000, 120000),
            'status' => 'completed',
            'sent_at' => now()->subDays(fake()->numberBetween(1, 14)),
        ];
    }
}
