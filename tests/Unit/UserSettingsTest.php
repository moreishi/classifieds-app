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
            ->assertSet('name', $this->user->name)
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
    public function it_updates_profile(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('name', 'New Name')
            ->call('updateProfile');

        $this->assertEquals('New Name', $this->user->fresh()->name);
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
            'gcash_number' => null,
            'gcash_verified_at' => null,
        ]);

        Livewire::actingAs($noGcash)
            ->test(UserSettings::class)
            ->assertSee('Not set');
    }
}
