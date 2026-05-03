<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_can_register_with_username(): void
    {
        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'new@example.com')->first();
        $this->assertEquals('newuser', $user->username);
        $this->assertEquals('newuser', $user->name); // name defaults to username
    }

    #[Test]
    public function it_rejects_duplicate_username(): void
    {
        User::factory()->create(['username' => 'taken']);

        $response = $this->post('/register', [
            'username' => 'taken',
            'email' => 'another@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function it_rejects_invalid_username(): void
    {
        $response = $this->post('/register', [
            'username' => 'invalid username!',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function it_still_requires_email_and_password(): void
    {
        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
