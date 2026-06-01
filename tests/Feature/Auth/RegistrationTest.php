<?php

namespace Tests\Feature\Auth;

use App\Models\DonorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_role_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register.user.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 1, Jakarta',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('user.dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
    }

    public function test_donatur_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register.donatur.store'), [
            'name' => 'Donatur Test',
            'email' => 'donatur@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '081234567891',
            'address' => 'Jl. Donatur No. 2, Jakarta',
            'store_name' => 'Toko Test',
            'store_description' => 'Deskripsi toko test',
            'store_address' => 'Jl. Toko No. 3, Jakarta',
            'store_latitude' => -6.2,
            'store_longitude' => 106.8,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('donatur.dashboard', absolute: false));

        $user = User::where('email', 'donatur@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('donatur', $user->role);
        $this->assertDatabaseHas('donor_profiles', [
            'user_id' => $user->id,
            'store_name' => 'Toko Test',
            'approval_status' => 'pending',
        ]);
    }

    public function test_user_registration_rejects_invalid_phone(): void
    {
        $response = $this->post(route('register.user.store'), [
            'name' => 'Test User',
            'email' => 'invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '12345',
            'address' => 'Jl. Test',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_role_middleware_blocks_wrong_dashboard(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->get(route('donatur.dashboard'))
            ->assertForbidden();
    }
}
