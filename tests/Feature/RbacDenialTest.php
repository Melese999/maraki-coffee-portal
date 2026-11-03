<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacDenialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_cashier_cannot_manage_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_cashier_cannot_delete_investments(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $this->assertFalse($user->can('investments.delete'));
    }
}
