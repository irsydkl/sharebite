<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donor_profiles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('store_name');

            $table->text('store_description')->nullable();

            $table->string('store_image')->nullable();

            $table->text('store_address');

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->boolean('is_verified')->default(false);

            $table->boolean('location_verified')->default(false);

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();

            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_profiles');
    }
};
