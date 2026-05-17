<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Payment\PaymentGateway;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerifyAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_verify_page(): void
    {
        $response = $this->get(route('verify-account'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function user_can_view_verify_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('verify-account'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_save_gcash_number(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\VerifyAccount::class)
            ->set('gcashNumber', '09171234567')
            ->call('saveNumber')
            ->assertSet('step', 'confirm');

        $this->assertEquals('09171234567', $this->user->fresh()->gcash_number);
    }

    #[Test]
    public function gcash_number_must_be_valid_format(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\VerifyAccount::class)
            ->set('gcashNumber', '12345')
            ->call('saveNumber')
            ->assertHasErrors(['gcashNumber']);
    }

    #[Test]
    public function gcash_number_must_start_with_09(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\VerifyAccount::class)
            ->set('gcashNumber', '12345678901')
            ->call('saveNumber')
            ->assertHasErrors(['gcashNumber']);
    }

    #[Test]
    public function verified_user_sees_done_step(): void
    {
        $verifiedUser = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => now(),
        ]);

        Livewire::actingAs($verifiedUser)
            ->test(\App\Livewire\VerifyAccount::class)
            ->assertSet('step', 'done')
            ->assertSet('isVerified', true);
    }

    #[Test]
    public function user_can_check_verification_status(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\VerifyAccount::class)
            ->call('checkVerificationStatus')
            ->assertSet('isVerified', false);
    }

    #[Test]
    public function user_without_number_shows_empty_step(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\VerifyAccount::class)
            ->assertSet('step', '');
    }

    #[Test]
    public function existing_gcash_number_is_loaded_in_form(): void
    {
        $userWithNumber = User::factory()->create([
            'gcash_number' => '09179876543',
        ]);

        Livewire::actingAs($userWithNumber)
            ->test(\App\Livewire\VerifyAccount::class)
            ->assertSet('gcashNumber', '09179876543');
    }
}
