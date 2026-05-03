<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class RegistrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function user_can_register_with_full_name_and_username(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('first_name', 'Juan')
                ->type('middle_name', 'Dela')
                ->type('last_name', 'Cruz')
                ->type('username', 'juandelacruz')
                ->type('email', 'juan@test.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->click('button[type=submit]')
                ->waitForLocation('/verify-email')
                ->assertPathIs('/verify-email')
                ->assertSee('verify your email');
        });
    }

    #[Test]
    public function registration_works_without_middle_name(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('first_name', 'Maria')
                ->type('last_name', 'Santos')
                ->type('username', 'msantos')
                ->type('email', 'maria@test.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->click('button[type=submit]')
                ->waitForLocation('/verify-email')
                ->assertPathIs('/verify-email')
                ->assertSee('verify your email');
        });
    }

    #[Test]
    public function registration_rejects_duplicate_email_and_username(): void
    {
        User::factory()->create([
            'username' => 'existing',
            'email' => 'existing@test.com',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('first_name', 'Test')
                ->type('last_name', 'User')
                ->type('username', 'existing')
                ->type('email', 'existing@test.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->click('button[type=submit]')
                ->waitForText('has already been taken')
                ->assertSee('has already been taken');
        });
    }

    #[Test]
    public function registration_rejects_invalid_username_characters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('first_name', 'Test')
                ->type('last_name', 'User')
                ->type('username', 'invalid user!')
                ->type('email', 'test@test.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->click('button[type=submit]')
                ->waitForText('dashes')
                ->assertSee('dashes');
        });
    }
}
