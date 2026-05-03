<?php

namespace Tests\Feature;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayMongoWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function validWebhookPayload(string $phone, array $overrides = []): array
    {
        return array_merge([
            'data' => [
                'id' => 'evt_test',
                'type' => 'event',
                'attributes' => [
                    'type' => 'payment.paid',
                    'livemode' => false,
                    'data' => [
                        'id' => 'pay_test',
                        'type' => 'payment',
                        'attributes' => [
                            'amount' => 500,
                            'currency' => 'PHP',
                            'payment_intent_id' => 'pi_test_abc',
                            'description' => 'Iskina.ph account verification',
                            'billing' => [
                                'phone' => $phone,
                                'email' => 'test@example.com',
                                'name' => 'Test User',
                                'address' => [
                                    'line1' => '',
                                    'city' => '',
                                    'state' => '',
                                    'country' => 'PH',
                                    'postal_code' => '',
                                ],
                            ],
                            'source' => [
                                'id' => 'src_test',
                                'type' => 'gcash',
                            ],
                            'status' => 'paid',
                            'metadata' => [],
                            'created_at' => now()->timestamp,
                            'paid_at' => now()->timestamp,
                            'updated_at' => now()->timestamp,
                        ],
                    ],
                    'created_at' => now()->timestamp,
                ],
            ],
        ], $overrides);
    }

    // ─── User verification via phone match ───────────────────

    #[Test]
    public function it_verifies_user_when_phone_matches_pending_verification()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        $payload = $this->validWebhookPayload('+639171234567');

        $response = $this->postJson('/webhooks/paymongo', $payload);

        $response->assertStatus(200);

        // User should now be verified
        $this->assertNotNull($user->fresh()->gcash_verified_at);
    }

    #[Test]
    public function it_handles_various_phone_formats()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        // PayMongo may send +639171234567 or 639171234567
        $payload = $this->validWebhookPayload('639171234567');

        $this->postJson('/webhooks/paymongo', $payload);

        $this->assertNotNull($user->fresh()->gcash_verified_at);
    }

    #[Test]
    public function it_verifies_user_via_metadata_user_id()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        $payload = $this->validWebhookPayload('+639171234567');
        // Override metadata directly — it's inside data.attributes.data.attributes.metadata
        $payload['data']['attributes']['data']['attributes']['metadata'] = [
            'user_id' => $user->id,
        ];

        $this->postJson('/webhooks/paymongo', $payload);

        $this->assertNotNull($user->fresh()->gcash_verified_at);
    }

    // ─── Edge cases ──────────────────────────────────────────

    #[Test]
    public function it_ignores_non_payment_events()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => null,
        ]);

        $payload = $this->validWebhookPayload('+639171234567');
        $payload['data']['attributes']['type'] = 'source.chargeable';

        $this->postJson('/webhooks/paymongo', $payload);

        $this->assertNull($user->fresh()->gcash_verified_at);
    }

    #[Test]
    public function it_returns_200_even_without_matching_user()
    {
        $payload = $this->validWebhookPayload('+639171234567');

        $response = $this->postJson('/webhooks/paymongo', $payload);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_handles_missing_phone_gracefully()
    {
        $payload = $this->validWebhookPayload('');
        $payload['data']['attributes']['data']['attributes']['billing']['phone'] = null;

        $response = $this->postJson('/webhooks/paymongo', $payload);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_does_not_reverify_already_verified_user()
    {
        $user = User::factory()->create([
            'gcash_number' => '09171234567',
            'gcash_verified_at' => now()->subDay(),
        ]);

        $originalVerifiedAt = $user->gcash_verified_at;

        $payload = $this->validWebhookPayload('+639171234567');

        $this->postJson('/webhooks/paymongo', $payload);

        $this->assertEquals(
            $originalVerifiedAt->timestamp,
            $user->fresh()->gcash_verified_at->timestamp
        );
    }

    // ─── Buy Credits Webhook ─────────────────────────────────

    private function buyCreditsPayload(int $userId, string $pack, int $credits, int $bonus = 0): array
    {
        return [
            'data' => [
                'id' => 'evt_test_buy',
                'type' => 'event',
                'attributes' => [
                    'type' => 'payment.paid',
                    'livemode' => false,
                    'data' => [
                        'id' => 'pay_test_buy',
                        'type' => 'payment',
                        'attributes' => [
                            'amount' => 5000,
                            'currency' => 'PHP',
                            'payment_intent_id' => 'pi_test_buy_123',
                            'description' => 'Buy 5000 listing credits',
                            'billing' => [
                                'phone' => '+639171234567',
                            ],
                            'status' => 'paid',
                            'metadata' => [
                                'user_id' => (string) $userId,
                                'type' => 'buy_credits',
                                'pack' => $pack,
                                'credits' => (string) $credits,
                                'bonus' => (string) $bonus,
                            ],
                            'created_at' => now()->timestamp,
                            'paid_at' => now()->timestamp,
                        ],
                    ],
                    'created_at' => now()->timestamp,
                ],
            ],
        ];
    }

    #[Test]
    public function it_deposits_credits_from_buy_credits_webhook()
    {
        $user = User::factory()->create(['credit_balance' => 0]);

        cache()->put("purchase:{$user->id}", [
            'gateway' => 'paymongo',
            'reference_id' => 'pi_test_buy_123',
            'pack' => 'basic',
            'credits' => 5000,
            'bonus' => 0,
            'amount' => 5000,
        ], 3600);

        $payload = $this->buyCreditsPayload($user->id, 'basic', 5000);

        $this->postJson('/webhooks/paymongo', $payload);

        $this->assertEquals(5000, $user->fresh()->credit_balance);

        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $user->id,
            'amount' => 5000,
            'type' => 'top_up',
        ]);
    }

    #[Test]
    public function it_deposits_credits_with_bonus_from_buy_credits_webhook()
    {
        $user = User::factory()->create(['credit_balance' => 0]);

        cache()->put("purchase:{$user->id}", [
            'gateway' => 'paymongo',
            'pack' => 'pro',
            'credits' => 50000,
            'bonus' => 10000,
            'amount' => 50000,
        ], 3600);

        $payload = $this->buyCreditsPayload($user->id, 'pro', 50000, 10000);

        $this->postJson('/webhooks/paymongo', $payload);

        // 50000 credits + 10000 bonus = 60000 total
        $this->assertEquals(60000, $user->fresh()->credit_balance);

        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $user->id,
            'amount' => 60000,
            'type' => 'top_up',
        ]);
    }

    #[Test]
    public function it_handles_buy_credits_without_pending_purchase()
    {
        $user = User::factory()->create(['credit_balance' => 0]);

        $payload = $this->buyCreditsPayload($user->id, 'basic', 5000);

        $this->postJson('/webhooks/paymongo', $payload);

        // Should still be 0
        $this->assertEquals(0, $user->fresh()->credit_balance);
    }

    #[Test]
    public function it_handles_buy_credits_for_nonexistent_user()
    {
        $payload = $this->buyCreditsPayload(99999, 'basic', 5000);

        $response = $this->postJson('/webhooks/paymongo', $payload);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_clears_pending_purchase_after_deposit()
    {
        $user = User::factory()->create(['credit_balance' => 0]);

        cache()->put("purchase:{$user->id}", [
            'gateway' => 'paymongo',
            'pack' => 'basic',
            'credits' => 5000,
            'bonus' => 0,
            'amount' => 5000,
        ], 3600);

        $payload = $this->buyCreditsPayload($user->id, 'basic', 5000);

        $this->postJson('/webhooks/paymongo', $payload);

        $this->assertNull(cache()->get("purchase:{$user->id}"));
    }
}
