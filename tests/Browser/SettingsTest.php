<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class SettingsTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'username' => 'juancruz',
            'email' => 'juan@test.com',
            'gcash_number' => '09171234567',
            'gcash_verified_at' => now(),
            'credit_balance' => 10000,
        ]);
    }

    #[Test]
    public function settings_page_shows_settings_heading(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/settings')
                ->waitForText('Settings')
                ->assertSee('Profile')
                ->assertSee('GCash & Credits')
                ->assertSee('Notifications');
        });
    }

    #[Test]
    public function settings_shows_gcash_and_credit_info(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/settings')
                ->waitForText('GCash')
                ->assertSee('09171234567');
        });
    }
}
