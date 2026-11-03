<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_to_amharic(): void
    {
        $this->get(route('locale.switch', 'am'))
            ->assertRedirect();

        $this->assertSame('am', session('locale'));
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $this->get(route('locale.switch', 'fr'))
            ->assertNotFound();
    }
}
