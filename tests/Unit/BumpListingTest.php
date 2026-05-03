<?php

namespace Tests\Unit;

use App\Livewire\BumpListing;
use App\Models\Listing;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BumpListingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'credit_balance' => 20000, // ₱200
        ]);
        $this->listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addDays(10),
        ]);
    }

    #[Test]
    public function it_bumps_a_listing_and_charges_credits()
    {
        Livewire::actingAs($this->user)
            ->test(BumpListing::class, ['listing' => $this->listing])
            ->set('selectedPlan', 'bump_7d')
            ->call('bump')
            ->assertHasNoErrors()
            ->assertSee('bumped')
            ->assertSee('7 Days');

        // Credits deducted: ₱200 - ₱50 = ₱150
        $this->assertEquals(15000, $this->user->fresh()->credit_balance);

        // featured_until set
        $this->assertNotNull($this->listing->fresh()->featured_until);
        $this->assertTrue($this->listing->fresh()->featured_until->isFuture());

        // Promotion record created
        $this->assertDatabaseHas('listing_promotions', [
            'listing_id' => $this->listing->id,
            'user_id' => $this->user->id,
            'plan' => 'bump_7d',
            'amount_paid' => 5000,
            'is_active' => true,
        ]);

        // Credit transaction logged
        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $this->user->id,
            'amount' => -5000,
            'type' => 'listing_bump',
            'reference_type' => Listing::class,
            'reference_id' => $this->listing->id,
        ]);
    }

    #[Test]
    public function it_rejects_non_owner()
    {
        $otherUser = User::factory()->create();

        Livewire::actingAs($otherUser)
            ->test(BumpListing::class, ['listing' => $this->listing])
            ->call('bump')
            ->assertSee('own listing');
    }

    #[Test]
    public function it_rejects_insufficient_credits()
    {
        $brokeUser = User::factory()->create(['credit_balance' => 100]); // ₱1
        $theirListing = Listing::factory()->create([
            'user_id' => $brokeUser->id,
            'status' => 'active',
        ]);

        Livewire::actingAs($brokeUser)
            ->test(BumpListing::class, ['listing' => $theirListing])
            ->call('bump')
            ->assertSee('Insufficient');
    }

    #[Test]
    public function it_rejects_duplicate_bump()
    {
        // First bump
        Livewire::actingAs($this->user)
            ->test(BumpListing::class, ['listing' => $this->listing])
            ->set('selectedPlan', 'bump_7d')
            ->call('bump')
            ->assertSee('bumped');

        // Second bump should fail
        Livewire::actingAs($this->user)
            ->test(BumpListing::class, ['listing' => $this->listing->fresh()])
            ->call('bump')
            ->assertSee('already promoted');
    }

    #[Test]
    public function it_extends_listing_expiry_when_bumping()
    {
        $originalExpiry = $this->listing->expires_at;

        Livewire::actingAs($this->user)
            ->test(BumpListing::class, ['listing' => $this->listing])
            ->set('selectedPlan', 'bump_7d')
            ->call('bump');

        // expires_at should be extended by 7 days
        $expected = $originalExpiry->copy()->addDays(7);
        $actual = $this->listing->fresh()->expires_at;

        $this->assertEquals(
            $expected->timestamp,
            $actual->timestamp,
            'Listing expiry should be extended by bump duration'
        );
    }

    #[Test]
    public function it_handles_14_day_plan()
    {
        Livewire::actingAs($this->user)
            ->test(BumpListing::class, ['listing' => $this->listing])
            ->set('selectedPlan', 'bump_14d')
            ->call('bump')
            ->assertHasNoErrors();

        // ₱200 - ₱80 = ₱120
        $this->assertEquals(12000, $this->user->fresh()->credit_balance);

        $this->assertDatabaseHas('listing_promotions', [
            'listing_id' => $this->listing->id,
            'plan' => 'bump_14d',
            'amount_paid' => 8000,
        ]);
    }

    #[Test]
    public function it_handles_30_day_plan()
    {
        Livewire::actingAs($this->user)
            ->test(BumpListing::class, ['listing' => $this->listing])
            ->set('selectedPlan', 'bump_30d')
            ->call('bump')
            ->assertHasNoErrors();

        // ₱200 - ₱140 = ₱60
        $this->assertEquals(6000, $this->user->fresh()->credit_balance);

        $this->assertDatabaseHas('listing_promotions', [
            'listing_id' => $this->listing->id,
            'plan' => 'bump_30d',
            'amount_paid' => 14000,
        ]);
    }
}
