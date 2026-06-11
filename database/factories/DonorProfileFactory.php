<?php

namespace Database\Factories;

use App\Models\DonorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonorProfile>
 */
class DonorProfileFactory extends Factory
{
    protected $model = DonorProfile::class;

    private static array $storeNames = [
        'Warung Nasi Bu Sari', 'Resto Padang Sederhana', 'Bakery Roti Lezat',
        'Kafe Kopi Nusantara', 'Rumah Makan Barokah', 'Catering Ibu Yanti',
        'Kedai Mie Ayam Pak Dedi', 'Toko Roti Gandum', 'Dapur Sehat Alami',
        'Kantin Sekolah Bu Dewi', 'Warung Sate Pak Budi', 'Restoran Sunda Asli',
        'Katering Harian Berkah', 'Toko Kue Manis', 'Warung Lalapan Segar',
    ];

    public function definition(): array
    {
        $name = fake()->randomElement(self::$storeNames).' '.fake()->numberBetween(1, 99);

        return [
            'user_id' => User::factory()->donatur(),
            'store_name' => $name,
            'store_description' => fake()->sentences(2, true),
            'store_image' => null,
            'store_address' => 'Jl. '.fake()->streetName().', Jakarta',
            'latitude' => fake()->latitude(-6.4, -6.0),
            'longitude' => fake()->longitude(106.6, 107.0),
            'is_verified' => false,
            'location_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
            'approval_status' => 'pending',
        ];
    }

    public function approved(?int $adminId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'location_verified' => true,
            'verified_by' => $adminId,
            'verified_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'approval_status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'location_verified' => false,
            'approval_status' => 'rejected',
        ]);
    }
}
