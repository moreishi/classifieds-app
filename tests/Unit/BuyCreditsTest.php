<?php

namespace Tests\Unit;

use App\Livewire\BuyCredits;
use App\Models\User;
use App\Services\Payment\ChargeResult;
use App\Services\Payment\PaymentGateway;
use App\Services\Payment\PayMongoGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BuyCreditsTest extends TestCase
{
    use DatabaseMigrations;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => now(),
            'credit_balance' => 0,
        ]);

        $mockGateway = $this->createMock(PayMongoGateway::class);
        $mockGateway->method('key')->willReturn('paymongo');
        $mockGateway->method('charge')->willReturn(new ChargeResult(
            success: true,
            referenceId: 'pi_test_123',
            redirectUrl: 'https://paymongo.dev/checkout/abc',
            message: 'Redirecting to GCash...',
        ));

        $this->app->instance(PaymentGateway::class, $mockGateway);
    }

    #[Test]
    public function it_shows_credit_packs(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->assertSee('₱50')
            ->assertSee('₱100')
            ->assertSee('₱200')
            ->assertSee('₱500')
            ->assertSee('50 credits')
            ->assertSee('100 credits')
            ->assertSee('200 credits')
            ->assertSee('500 credits');
    }

    #[Test]
    public function it_shows_bonus_labels(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->assertSee('bonus');
    }

    #[Test]
    public function it_shows_current_balance(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->assertSee('₱0.00');
    }

    #[Test]
    public function it_requires_gcash_number(): void
    {
        $userWithoutGcash = User::factory()->create([
            'gcash_number' => null,
        ]);

        Livewire::actingAs($userWithoutGcash)
            ->test(BuyCredits::class)
            ->assertSee('verify your GCash account');
    }

    #[Test]
    public function it_starts_payment_on_buy(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->set('selectedPack', 'basic')
            ->call('buy')
            ->assertDispatched('redirect-to-checkout');
    }

    #[Test]
    public function it_stores_pending_purchase_in_cache(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->set('selectedPack', 'basic')
            ->call('buy');

        $this->assertNotNull(cache()->get("purchase:{$this->user->id}"));
    }

    #[Test]
    public function it_validates_invalid_pack(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->set('selectedPack', 'nonexistent')
            ->call('buy')
            ->assertSee('Invalid pack selected');
    }

    #[Test]
    public function it_handles_boost_pack_bonus(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->set('selectedPack', 'boost')
            ->call('buy');

        $pending = cache()->get("purchase:{$this->user->id}");
        $this->assertEquals(20000, $pending['credits']);
        $this->assertEquals(2000, $pending['bonus']);
    }

    #[Test]
    public function it_handles_pro_pack_bonus(): void
    {
        Livewire::actingAs($this->user)
            ->test(BuyCredits::class)
            ->set('selectedPack', 'pro')
            ->call('buy');

        $pending = cache()->get("purchase:{$this->user->id}");
        $this->assertEquals(50000, $pending['credits']);
        $this->assertEquals(10000, $pending['bonus']);
    }
}
