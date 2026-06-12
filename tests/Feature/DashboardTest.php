<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_donatur_can_view_dashboard_with_chart_data(): void
    {
        $donatur = User::factory()->donatur()->create();

        $response = $this
            ->actingAs($donatur)
            ->get(route('donatur.dashboard'));

        $response->assertOk();
        $response->assertViewHas('portionsHistory');
        $response->assertViewHas('approved');
        $response->assertViewHas('pending');
        $response->assertViewHas('rejected');
    }

    public function test_admin_can_view_dashboard_with_chart_data(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('paymentsHistory');
        $response->assertViewHas('foodCategories');
        $response->assertViewHas('userRoles');
    }

    public function test_guest_can_view_landing_page_with_foods(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewHas('foods');
    }
}
