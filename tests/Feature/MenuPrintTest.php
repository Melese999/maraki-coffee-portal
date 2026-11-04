<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_download_selected_menu_pdf(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $kept = MenuItem::factory()->create(['name' => 'ቡና', 'is_active' => true]);
        MenuItem::factory()->create(['name' => 'ሻይ', 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('menu-items.print', ['ids' => [$kept->id]]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_without_ids_uses_active_items(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        MenuItem::factory()->create(['name' => 'ንቁ', 'is_active' => true]);
        MenuItem::factory()->create(['name' => 'የተደበቀ', 'is_active' => false]);

        $this->actingAs($user)
            ->get(route('menu-items.print'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_without_permission_cannot_download_menu_pdf(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('menu-items.print'))
            ->assertForbidden();
    }
}
