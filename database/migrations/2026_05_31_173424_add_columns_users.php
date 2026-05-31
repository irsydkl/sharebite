<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->enum('role', [
                'admin',
                'donatur',
                'user',
            ])->default('user');

            $table->string('phone')->nullable();

            $table->text('address')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->decimal('balance', 12, 2)
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->string('profile_photo')
                ->nullable();

            $table->boolean('is_verified')
                ->default(false);

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamp('last_login_at')
                ->nullable();

            $table->string('last_login_ip')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'role',
                'phone',
                'address',
                'latitude',
                'longitude',
                'balance',
                'is_active',
                'profile_photo',
                'is_verified',
                'verified_at',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
