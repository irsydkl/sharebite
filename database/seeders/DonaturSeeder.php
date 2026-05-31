<?php

namespace Database\Seeders;

use App\Models\DonorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DonaturSeeder extends Seeder
{
    public function run(): void
    {
        $donatur = User::updateOrCreate(
            ['email' => 'donatur@sharebite.test'],
            [
                'name' => 'Donatur Sharebite',
                'password' => 'password',
                'role' => 'donatur',
                'phone' => '081234567891',
                'address' => 'Jl. Makanan Sehat No. 10, Jakarta',
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'is_active' => true,
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        DonorProfile::updateOrCreate(
            ['user_id' => $donatur->id],
            [
                'store_name' => 'Toko Makanan Sehat',
                'store_description' => 'Menyediakan makanan berlebih berkualitas dengan harga terjangkau.',
                'store_address' => 'Jl. Makanan Sehat No. 10, Jakarta',
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'is_verified' => true,
                'location_verified' => true,
                'approval_status' => 'approved',
                'verified_at' => now(),
            ],
        );
    }
}
