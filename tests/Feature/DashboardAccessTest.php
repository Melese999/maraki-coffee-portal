<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('ETB', false);
    }

    public function test_dashboard_accepts_date_range_filter(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)
            ->get(route('dashboard', [
                'from' => now()->subDays(3)->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertViewHas('from', now()->subDays(3)->toDateString())
            ->assertViewHas('to', now()->toDateString())
            ->assertViewHas('dayCount', 4);
    }

    public function test_dashboard_preset_today_sets_single_day_range(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)
            ->get(route('dashboard', ['preset' => 'today']))
            ->assertOk()
            ->assertViewHas('from', today()->toDateString())
            ->assertViewHas('to', today()->toDateString())
            ->assertViewHas('preset', 'today');
    }

    public function test_user_without_permission_cannot_view_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertForbidden();
    }
}
