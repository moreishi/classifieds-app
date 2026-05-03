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
    public function it_can_register_with_full_name_and_username(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'username' => 'juandelacruz',
            'email' => 'juan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'juan@example.com')->first();
        $this->assertEquals('juandelacruz', $user->username);
        $this->assertEquals('Juan', $user->first_name);
        $this->assertEquals('Dela', $user->middle_name);
        $this->assertEquals('Cruz', $user->last_name);
        $this->assertEquals('Juan Dela Cruz', $user->name); // full name stored
    }

    #[Test]
    public function it_rejects_duplicate_username(): void
    {
        User::factory()->create(['username' => 'taken']);

        $response = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'username' => 'taken',
            'email' => 'jane@example.com',
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
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'username' => 'invalid username!',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    #[Test]
    public function it_requires_name_fields(): void
    {
        $response = $this->post('/register', [
            'first_name' => '',
            'last_name' => '',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name']);
    }

    #[Test]
    public function it_allows_registration_without_middle_name(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'username' => 'msantos',
            'email' => 'maria@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'maria@example.com')->first();
        $this->assertNull($user->middle_name);
        $this->assertEquals('Maria Santos', $user->name);
    }

    #[Test]
    public function it_still_requires_email_and_password(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'newuser',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
