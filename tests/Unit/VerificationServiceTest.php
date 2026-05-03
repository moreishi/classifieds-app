<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Payment\PayMongoGateway;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private VerificationService $service;
    private PayMongoGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VerificationService();
        $this->gateway = new PayMongoGateway();
    }

    #[Test]
    public function it_starts_verification_and_stores_pending()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        $result = $this->service->startVerification($user, $this->gateway);

        $this->assertArrayHasKey('reference_id', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('checkout_url', $result);
        $this->assertStringContainsString('5.00', $result['message']);

        // Pending should be cached
        $pending = cache()->get("verification:{$user->id}");
        $this->assertNotNull($pending);
        $this->assertEquals('paymongo', $pending['gateway']);
        $this->assertNotNull($pending['reference_id']);
    }

    #[Test]
    public function it_throws_if_no_gcash_number()
    {
        $user = User::factory()->create(['gcash_number' => null]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No GCash number');

        $this->service->startVerification($user, $this->gateway);
    }

    #[Test]
    public function it_throws_if_already_verified()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => now(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('already verified');

        $this->service->startVerification($user, $this->gateway);
    }

    #[Test]
    public function it_confirms_verification_and_sets_verified_at()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        // Start verification first
        $result = $this->service->startVerification($user, $this->gateway);

        // Verify user is not yet marked
        $this->assertNull($user->fresh()->gcash_verified_at);

        // Confirm (in dev mode, this always succeeds via the gateway)
        $this->service->confirmVerification($user, $this->gateway, 500);

        // User should now be verified
        $this->assertNotNull($user->fresh()->gcash_verified_at);

        // Pending should be cleared
        $this->assertNull(cache()->get("verification:{$user->id}"));
    }

    #[Test]
    public function it_throws_if_no_pending_verification()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No pending verification');

        $this->service->confirmVerification($user, $this->gateway, 500);
    }

    #[Test]
    public function it_throws_on_gateway_mismatch()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        // Start with PayMongo
        $this->service->startVerification($user, $this->gateway);

        // Tamper the cache to simulate wrong gateway
        cache()->put("verification:{$user->id}", [
            'gateway' => 'gcash',
            'reference_id' => 'something',
            'amount_charged' => 500,
            'started_at' => now(),
        ], 60);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('gateway mismatch');

        $this->service->confirmVerification($user, $this->gateway, 500);
    }

    #[Test]
    public function it_detects_verified_user()
    {
        $verified = User::factory()->create([
            'gcash_verified_at' => now(),
        ]);
        $unverified = User::factory()->create([
            'gcash_verified_at' => null,
        ]);

        $this->assertTrue($this->service->isVerified($verified));
        $this->assertFalse($this->service->isVerified($unverified));
    }

    #[Test]
    public function it_detects_pending_verification()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
        ]);

        $this->assertFalse($this->service->hasPendingVerification($user));

        $this->service->startVerification($user, $this->gateway);

        $this->assertTrue($this->service->hasPendingVerification($user));
        $this->assertNotNull($this->service->getPendingVerification($user));
    }
}
