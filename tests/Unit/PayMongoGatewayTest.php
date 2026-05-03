<?php

namespace Tests\Unit;

use App\Services\Payment\PayMongoGateway;
use App\Services\Payment\PaymentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayMongoGatewayTest extends TestCase
{
    private PayMongoGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new PayMongoGateway();
    }

    // ─── verifyAccount ───────────────────────────────────────

    #[Test]
    public function it_validates_a_valid_gcash_number()
    {
        $result = $this->gateway->verifyAccount('09171234567');

        $this->assertTrue($result->success);
        $this->assertStringContainsString('valid', strtolower($result->message));
    }

    #[Test]
    public function it_rejects_invalid_gcash_number()
    {
        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid mobile number');

        $this->gateway->verifyAccount('12345');
    }

    #[Test]
    public function it_rejects_non_philippine_number()
    {
        $this->expectException(PaymentException::class);

        $this->gateway->verifyAccount('+14155551234');
    }

    // ─── chargeForVerification (dev/test mode) ───────────────

    #[Test]
    public function it_creates_a_verification_charge_in_dev_mode()
    {
        $result = $this->gateway->chargeForVerification(
            accountId: '09171234567',
            amountCentavos: 500,
            metadata: ['user_id' => 1],
        );

        $this->assertTrue($result->success);
        $this->assertEquals(500, $result->amountCharged);
        $this->assertNotNull($result->referenceId);
        $this->assertStringContainsString('5.00', $result->message);
        $this->assertStringContainsString('pay', strtolower($result->message));

        // Metadata should include checkout URL for redirect
        $this->assertArrayHasKey('checkout_url', $result->metadata);
        $this->assertArrayHasKey('payment_intent_id', $result->metadata);
    }

    #[Test]
    public function it_uses_default_amount_when_zero_passed()
    {
        $result = $this->gateway->chargeForVerification(
            accountId: '09171234567',
            amountCentavos: 0,
        );

        // Default is 500 centavos (₱5)
        $this->assertEquals(500, $result->amountCharged);
    }

    #[Test]
    public function it_uses_custom_amount_when_provided()
    {
        $result = $this->gateway->chargeForVerification(
            accountId: '09171234567',
            amountCentavos: 5000, // ₱50
        );

        $this->assertEquals(5000, $result->amountCharged);
    }

    #[Test]
    public function it_rejects_invalid_number_on_charge()
    {
        $this->expectException(PaymentException::class);

        $this->gateway->chargeForVerification('abc', 500);
    }

    // ─── confirmVerification (dev/test mode) ─────────────────

    #[Test]
    public function it_confirms_verification_in_dev_mode()
    {
        $result = $this->gateway->confirmVerification(
            referenceId: 'pi_dev_test123',
            amountCentavos: 500,
        );

        $this->assertTrue($result->success);
        $this->assertEquals(500, $result->amountCharged);
        $this->assertStringContainsString('verified', strtolower($result->message));
    }

    // ─── label / key ─────────────────────────────────────────

    #[Test]
    public function it_has_a_label()
    {
        $label = $this->gateway->label();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    #[Test]
    public function it_has_a_key()
    {
        $this->assertEquals('paymongo', $this->gateway->key());
    }
}
