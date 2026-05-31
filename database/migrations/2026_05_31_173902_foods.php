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
        Schema::create('foods', function (Blueprint $table) {

            $table->id();

            $table->foreignId('donor_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('category_id')
                ->constrained('product_categories')
                ->onDelete('cascade');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->integer('quantity');

            $table->integer('remaining_quantity');

            $table->string('unit')->default('pcs');

            $table->decimal('original_price', 12, 2);

            $table->decimal('service_fee', 12, 2);

            $table->decimal('final_price', 12, 2);

            $table->text('pickup_address');

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->timestamp('pickup_start');

            $table->timestamp('pickup_end');

            $table->timestamp('pickup_deadline');

            $table->integer('pickup_duration_minutes')
                ->default(60);

            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->enum('status', [
                'available',
                'claimed',
                'completed',
                'expired',
                'cancelled',
            ])->default('available');

            $table->timestamp('expired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
