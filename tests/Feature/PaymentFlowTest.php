<?php

namespace Tests\Feature;

use App\Models\DonorProfile;
use App\Models\Food;
use App\Models\FoodClaim;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_resolves_booking_code_from_order_id_with_timestamp(): void
    {
        $user = User::factory()->user()->create();
        $donatur = User::factory()->donatur()->create();
        DonorProfile::factory()->approved()->create(['user_id' => $donatur->id]);

        $food = Food::factory()->create([
            'donor_id' => $donatur->id,
            'status' => 'available',
            'approval_status' => 'approved',
            'remaining_quantity' => 10,
            'quantity' => 10,
        ]);

        $claim = FoodClaim::factory()->create([
            'food_id' => $food->id,
            'user_id' => $user->id,
            'booking_code' => 'BK-TEST1234',
            'claim_status' => 'waiting_payment',
            'quantity_claimed' => 2,
        ]);

        Payment::factory()->forClaim($claim)->create([
            'transaction_reference' => 'BK-TEST1234-1718123456',
        ]);

        $service = app(PaymentService::class);

        $this->assertSame('BK-TEST1234', $service->resolveBookingCodeFromOrderId('BK-TEST1234-1718123456'));
        $this->assertNotNull($service->resolveClaimFromOrderId('BK-TEST1234-1718123456'));
    }

    public function test_webhook_marks_payment_paid_and_moves_claim_to_ready_pickup(): void
    {
        $user = User::factory()->user()->create();
        $donatur = User::factory()->donatur()->create();
        DonorProfile::factory()->approved()->create(['user_id' => $donatur->id]);

        $food = Food::factory()->create([
            'donor_id' => $donatur->id,
            'status' => 'claimed',
            'approval_status' => 'approved',
            'remaining_quantity' => 8,
            'quantity' => 10,
        ]);

        $claim = FoodClaim::factory()->create([
            'food_id' => $food->id,
            'user_id' => $user->id,
            'booking_code' => 'BK-PAID1234',
            'claim_status' => 'waiting_payment',
        ]);

        $payment = Payment::factory()->forClaim($claim)->create([
            'transaction_reference' => 'BK-PAID1234-1718123456',
        ]);

        app(PaymentService::class)->handleWebhook([
            'order_id' => 'BK-PAID1234-1718123456',
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ]);

        $claim->refresh();
        $payment->refresh();

        $this->assertSame('ready_pickup', $claim->claim_status);
        $this->assertSame('paid', $payment->payment_status);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_complete_pickup_credits_donor_balance_and_creates_pending_payout(): void
    {
        $user = User::factory()->user()->create();
        $donatur = User::factory()->donatur()->create(['balance' => 0]);
        DonorProfile::factory()->approved()->create(['user_id' => $donatur->id]);

        $food = Food::factory()->create([
            'donor_id' => $donatur->id,
            'approval_status' => 'approved',
            'quantity' => 1,
            'final_price' => 50000,
        ]);

        $claim = FoodClaim::factory()->create([
            'food_id' => $food->id,
            'user_id' => $user->id,
            'claim_status' => 'ready_pickup',
            'quantity_claimed' => 1,
        ]);

        Payment::factory()->forClaim($claim)->paid()->create();

        app(PaymentService::class)->completePickup($claim);

        $donatur->refresh();
        $claim->refresh();

        $this->assertSame('completed', $claim->claim_status);
        $this->assertSame(50000.0, (float) $donatur->balance);
        $this->assertDatabaseHas('payouts', [
            'donor_id' => $donatur->id,
            'amount' => 50000,
            'status' => 'pending',
        ]);
    }

    public function test_admin_process_payout_debits_donor_balance(): void
    {
        $donatur = User::factory()->donatur()->create(['balance' => 75000]);

        $payout = Payout::factory()->create([
            'donor_id' => $donatur->id,
            'amount' => 50000,
            'status' => 'pending',
        ]);

        app(PaymentService::class)->processPayout($payout);

        $donatur->refresh();
        $payout->refresh();

        $this->assertSame(25000.0, (float) $donatur->balance);
        $this->assertSame('completed', $payout->status);
        $this->assertNotNull($payout->sent_at);
    }

    public function test_mock_webhook_endpoint_works_in_testing(): void
    {
        $user = User::factory()->user()->create();
        $donatur = User::factory()->donatur()->create();
        DonorProfile::factory()->approved()->create(['user_id' => $donatur->id]);

        $food = Food::factory()->create([
            'donor_id' => $donatur->id,
            'status' => 'claimed',
            'approval_status' => 'approved',
        ]);

        $claim = FoodClaim::factory()->create([
            'food_id' => $food->id,
            'user_id' => $user->id,
            'booking_code' => 'BK-MOCK1234',
            'claim_status' => 'waiting_payment',
        ]);

        Payment::factory()->forClaim($claim)->create([
            'transaction_reference' => 'BK-MOCK1234-1718123456',
        ]);

        $this->postJson('/webhook/midtrans', [
            'order_id' => 'BK-MOCK1234-1718123456',
            'transaction_status' => 'settlement',
            'is_mock_simulation' => true,
        ])->assertOk();

        $this->assertSame('ready_pickup', $claim->fresh()->claim_status);
    }

    public function test_create_snap_token_handles_empty_phone(): void
    {
        $user = User::factory()->user()->create(['phone' => null]);
        $donatur = User::factory()->donatur()->create();
        DonorProfile::factory()->approved()->create(['user_id' => $donatur->id]);

        $food = Food::factory()->create([
            'donor_id' => $donatur->id,
            'approval_status' => 'approved',
        ]);

        $claim = FoodClaim::factory()->create([
            'food_id' => $food->id,
            'user_id' => $user->id,
            'claim_status' => 'waiting_payment',
        ]);

        Payment::factory()->forClaim($claim)->create();

        $service = app(PaymentService::class);
        $token = $service->createSnapToken($claim);

        $this->assertNotNull($token);
    }
}
