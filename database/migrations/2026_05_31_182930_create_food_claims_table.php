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
        Schema::create('food_claims', function (Blueprint $table) {

            $table->id();
        
            $table->foreignId('food_id')
                ->constrained()
                ->onDelete('cascade');
        
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
        
            $table->integer('quantity_claimed');
        
            $table->decimal('subtotal_price', 12, 2);
        
            $table->decimal('service_fee', 12, 2);
        
            $table->decimal('total_price', 12, 2);
        
            $table->string('booking_code')->unique();
        
            $table->timestamp('payment_expired_at');
        
            $table->timestamp('pickup_deadline');
        
            $table->timestamp('picked_up_at')->nullable();
        
            $table->string('pickup_proof')->nullable();
        
            $table->enum('claim_status', [
        
                'waiting_payment',
        
                'paid',
        
                'ready_pickup',
        
                'picked_up',
        
                'completed',
        
                'expired',
        
                'cancelled'
        
            ])->default('waiting_payment');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_claims');
    }
};
