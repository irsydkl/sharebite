<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'user@sharebite.test'],
            [
                'name' => 'User Sharebite',
                'password' => 'password',
                'role' => 'user',
                'phone' => '081234567892',
                'address' => 'Jl. Pengguna No. 5, Jakarta',
                'latitude' => -6.1750000,
                'longitude' => 106.8200000,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
