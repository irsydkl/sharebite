<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sharebite.test'],
            [
                'name' => 'Admin Sharebite',
                'password' => 'password',
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ],
        );
    }
}
