<?php

namespace Database\Factories;

use App\Models\FoodClaim;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $donorAmount = fake()->randomFloat(2, 15000, 100000);
        $serviceFee = round($donorAmount * 0.1, 2);

        return [
            'claim_id' => FoodClaim::factory(),
            'user_id' => User::factory(),
            'amount' => $donorAmount + $serviceFee,
            'service_fee' => $serviceFee,
            'donor_amount' => $donorAmount,
            'payment_method' => fake()->randomElement(['qris', 'bank_transfer', 'ewallet']),
            'payment_status' => 'pending',
            'transaction_reference' => null,
            'paid_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Payment $payment): void {
            $claim = $payment->claim ?? ($payment->claim_id ? FoodClaim::find($payment->claim_id) : null);

            if ($claim === null) {
                return;
            }

            $payment->user_id = $claim->user_id;
            $payment->amount = $claim->total_price;
            $payment->service_fee = $claim->service_fee;
            $payment->donor_amount = $claim->subtotal_price;
        });
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'transaction_reference' => strtoupper(fake()->bothify('TRX-########')),
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
            'paid_at' => null,
        ]);
    }

    public function forClaim(FoodClaim $claim): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_id' => $claim->id,
            'user_id' => $claim->user_id,
            'amount' => $claim->total_price,
            'service_fee' => $claim->service_fee,
            'donor_amount' => $claim->subtotal_price,
        ]);
    }
}
