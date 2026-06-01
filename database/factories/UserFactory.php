<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-6.4, -6.0),
            'longitude' => fake()->longitude(106.6, 107.0),
            'balance' => fake()->randomFloat(2, 0, 500000),
            'is_active' => true,
            'profile_photo' => null,
            'is_verified' => false,
            'verified_at' => null,
            'last_login_at' => null,
            'last_login_ip' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function donatur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'donatur',
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }
}
