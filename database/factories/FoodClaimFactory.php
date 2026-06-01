<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\FoodClaim;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FoodClaim>
 */
class FoodClaimFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotalPrice = fake()->randomFloat(2, 15000, 100000);
        $serviceFee = round($subtotalPrice * 0.1, 2);

        return [
            'food_id' => Food::factory(),
            'user_id' => User::factory(),
            'quantity_claimed' => fake()->numberBetween(1, 3),
            'subtotal_price' => $subtotalPrice,
            'service_fee' => $serviceFee,
            'total_price' => $subtotalPrice + $serviceFee,
            'booking_code' => strtoupper('BK-'.Str::random(8)),
            'payment_expired_at' => now()->addMinutes(30),
            'pickup_deadline' => now()->addHours(4),
            'picked_up_at' => null,
            'pickup_proof' => null,
            'claim_status' => 'waiting_payment',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (FoodClaim $claim): void {
            $food = $claim->food ?? ($claim->food_id ? Food::find($claim->food_id) : null);

            if ($food === null) {
                return;
            }

            $unitPrice = $food->final_price / max($food->quantity, 1);
            $subtotalPrice = round($unitPrice * $claim->quantity_claimed, 2);
            $serviceFee = round($subtotalPrice * 0.1, 2);

            $claim->subtotal_price = $subtotalPrice;
            $claim->service_fee = $serviceFee;
            $claim->total_price = $subtotalPrice + $serviceFee;
            $claim->pickup_deadline = $food->pickup_deadline;
        });
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_status' => 'paid',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_status' => 'completed',
            'picked_up_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_status' => 'cancelled',
        ]);
    }
}
