<?php

namespace Tests\Unit;

use App\Livewire\UserSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use DatabaseMigrations;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'gcash_number' => '09171234567',
            'gcash_verified_at' => now(),
            'credit_balance' => 5000,
        ]);
    }

    #[Test]
    public function it_shows_profile_info(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertSet('firstName', 'Test')
            ->assertSet('lastName', 'User')
            ->assertSet('username', 'testuser')
            ->assertSet('email', $this->user->email);
    }

    #[Test]
    public function it_shows_gcash_status(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertSet('gcashNumber', '09171234567');
    }

    #[Test]
    public function it_shows_credit_balance(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertSee('₱50.00');
    }

    #[Test]
    public function it_shows_notification_preferences(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertSet('notifyNewInquiry', true)
            ->assertSet('notifySellerReply', true);
    }

    #[Test]
    public function it_updates_name_fields(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('firstName', 'Juan')
            ->set('middleName', 'Dela')
            ->set('lastName', 'Cruz')
            ->call('updateProfile');

        $fresh = $this->user->fresh();
        $this->assertEquals('Juan', $fresh->first_name);
        $this->assertEquals('Dela', $fresh->middle_name);
        $this->assertEquals('Cruz', $fresh->last_name);
        $this->assertEquals('Juan Dela Cruz', $fresh->name);
    }

    #[Test]
    public function it_updates_username(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('username', 'newhandle')
            ->call('updateProfile');

        $this->assertEquals('newhandle', $this->user->fresh()->username);
    }

    #[Test]
    public function it_updates_notification_preferences(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('notifyNewInquiry', false)
            ->call('updateNotifications');

        $this->assertFalse($this->user->fresh()->notify_new_inquiry);
    }

    #[Test]
    public function it_shows_unverified_state_when_not_verified(): void
    {
        $unverified = User::factory()->create([
            'first_name' => 'Unverified',
            'last_name' => 'User',
            'username' => 'unverified',
            'gcash_number' => '09179999999',
            'gcash_verified_at' => null,
        ]);

        Livewire::actingAs($unverified)
            ->test(UserSettings::class)
            ->assertSee('Not verified');
    }

    #[Test]
    public function it_shows_not_set_when_no_gcash(): void
    {
        $noGcash = User::factory()->create([
            'first_name' => 'No',
            'last_name' => 'Gcash',
            'username' => 'nogcash',
            'gcash_number' => null,
            'gcash_verified_at' => null,
        ]);

        Livewire::actingAs($noGcash)
            ->test(UserSettings::class)
            ->assertSee('Not set');
    }

    #[Test]
    public function it_rejects_duplicate_username(): void
    {
        User::factory()->create([
            'username' => 'taken',
            'email' => 'other@example.com',
        ]);

        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('username', 'taken')
            ->call('updateProfile')
            ->assertHasErrors('username');
    }

    #[Test]
    public function it_rejects_invalid_username_characters(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('username', 'invalid username!')
            ->call('updateProfile')
            ->assertHasErrors('username');
    }
}
