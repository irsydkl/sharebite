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
        Schema::create('approval_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('food_id')
                ->nullable()
                ->constrained('foods')
                ->nullOnDelete();

            $table->foreignId('donor_profile_id')
                ->nullable()
                ->constrained('donor_profiles')
                ->nullOnDelete();

            $table->enum('status', [
                'approved',
                'rejected',
            ]);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
