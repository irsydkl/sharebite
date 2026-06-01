<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(5, 30);
        $originalPrice = fake()->randomFloat(2, 25000, 150000);
        $serviceFee = round($originalPrice * 0.1, 2);
        $finalPrice = $originalPrice + $serviceFee;
        $pickupStart = now()->addHour();
        $pickupEnd = $pickupStart->copy()->addHours(3);
        $pickupDeadline = $pickupEnd->copy()->addHour();

        return [
            'donor_id' => User::factory()->donatur(),
            'category_id' => fn () => ProductCategory::query()->inRandomOrder()->value('id')
                ?? ProductCategory::create([
                    'name' => fake()->unique()->words(2, true),
                    'description' => fake()->sentence(),
                ])->id,
            'approved_by' => null,
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'quantity' => $quantity,
            'remaining_quantity' => $quantity,
            'unit' => fake()->randomElement(['pcs', 'porsi', 'box']),
            'original_price' => $originalPrice,
            'service_fee' => $serviceFee,
            'final_price' => $finalPrice,
            'pickup_address' => fake()->address(),
            'latitude' => fake()->latitude(-6.4, -6.0),
            'longitude' => fake()->longitude(106.6, 107.0),
            'pickup_start' => $pickupStart,
            'pickup_end' => $pickupEnd,
            'pickup_deadline' => $pickupDeadline,
            'pickup_duration_minutes' => 60,
            'approval_status' => 'approved',
            'status' => 'available',
            'expired_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'pending',
            'approved_by' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'approved',
            'approved_by' => User::factory()->admin(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expired_at' => now(),
        ]);
    }
}
