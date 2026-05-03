<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PayMongo gateway for GCash account verification.
 *
 * Flow:
 *   1. User enters GCash number
 *   2. User clicks "Verify — pay ₱5"
 *   3. PayMongo creates a GCash Payment Intent
 *   4. User redirected to GCash app to approve ₱5
 *   5. PayMongo webhook fires (payment.paid) with billing.phone = their number
 *   6. We match the phone → mark user verified
 *
 * Fees: GCash e-wallet = 2.23% (₱0.11 on ₱5)
 *
 * @see https://developers.paymongo.com/docs/pipm-workflow
 */
class PayMongoGateway implements PaymentGateway
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl;

    /**
     * The verification amount in centavos.
     * ₱5 = 500 centavos — just enough to prove ownership without being a barrier.
     * Low fee (₱0.11), easy upsell to ₱50 credits after.
     */
    private const int VERIFY_AMOUNT_CENTAVOS = 500; // ₱5.00

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
        $this->publicKey = config('services.paymongo.public_key');
        $this->baseUrl = config('services.paymongo.base_url', 'https://api.paymongo.com/v1');
    }

    /**
     * Validate the GCash number format.
     * No external check needed — the real validation is the payment itself.
     */
    public function verifyAccount(string $accountId, array $metadata = []): VerificationResult
    {
        $this->validateNumber($accountId);
        return new VerificationResult(
            success: true,
            referenceId: 'fmt-' . Str::random(8),
            message: 'Number format valid.',
        );
    }

    /**
     * Create a Payment Intent that the user pays via GCash.
     *
     * This returns a checkout URL where the user authenticates with GCash.
     * Once paid, the webhook handler matches billing.phone to mark verified.
     *
     * @throws PaymentException
     */
    public function chargeForVerification(string $accountId, int $amountCentavos, array $metadata = []): VerificationResult
    {
        $this->validateNumber($accountId);
        $amount = $amountCentavos > 0 ? $amountCentavos : self::VERIFY_AMOUNT_CENTAVOS;
        $pesos = number_format($amount / 100, 2);

        if (app()->environment('local', 'testing')) {
            $fakeRef = 'pi_dev_' . Str::random(20);
            $fakeUrl = "https://checkout.paymongo.com/dev/{$fakeRef}";

            Log::info("[PayMongo] dev mode: verification payment for {$accountId}, ₱{$pesos}");

            return new VerificationResult(
                success: true,
                referenceId: $fakeRef,
                message: "Pay ₱{$pesos} to verify your GCash. After payment, your account will be verified automatically.",
                amountCharged: $amount,
                metadata: [
                    'checkout_url' => $fakeUrl,
                    'payment_intent_id' => $fakeRef,
                ],
            );
        }

        // Production: Create a Payment Intent for GCash
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment-intents", [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'payment_method_allowed' => ['gcash'],
                        'description' => 'Iskina.ph account verification',
                        'statement_descriptor' => 'ISKINA VERIFY',
                        'metadata' => [
                            'user_id' => $metadata['user_id'] ?? null,
                            'gcash_number' => $accountId,
                            'type' => 'account_verification',
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            $detail = $response->json('errors.0.detail', 'Unknown error');
            Log::error("[PayMongo] Payment Intent creation failed: {$detail}", [
                'response' => $response->body(),
            ]);
            throw new PaymentException("Failed to create payment: {$detail}");
        }

        $piId = $response->json('data.id');
        $clientKey = $response->json('data.attributes.client_key');
        $checkoutUrl = "https://pay.paymongo.com/gcash/{$piId}?client_key={$clientKey}";

        Log::info("[PayMongo] Payment Intent created: {$piId} for {$accountId}");

        return new VerificationResult(
            success: true,
            referenceId: $piId,
            message: "Pay ₱{$pesos} to verify your GCash account. After payment, we'll verify automatically.",
            amountCharged: $amount,
            metadata: [
                'checkout_url' => $checkoutUrl,
                'client_key' => $clientKey,
                'payment_intent_id' => $piId,
            ],
        );
    }

    /**
     * Confirm verification by checking the Payment Intent status.
     * Called after the webhook has processed the payment.
     */
    public function confirmVerification(string $referenceId, int $amountCentavos): VerificationResult
    {
        if (app()->environment('local', 'testing')) {
            Log::info("[PayMongo] confirmVerification (dev): {$referenceId}");
            return new VerificationResult(
                success: true,
                referenceId: $referenceId,
                message: 'Account verified successfully!',
                amountCharged: $amountCentavos,
            );
        }

        // Check the Payment Intent status via API
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/payment-intents/{$referenceId}");

        if ($response->failed()) {
            throw new PaymentException("Failed to retrieve payment status.");
        }

        $attributes = $response->json('data.attributes', []);
        $status = $attributes['status'] ?? 'unknown';
        $payments = $attributes['payments'] ?? [];
        $lastPayment = $payments[0] ?? [];

        if ($status !== 'succeeded' && $status !== 'paid') {
            throw new PaymentException(
                "Payment status is '{$status}'. Please wait for the payment to complete."
            );
        }

        // Optional: verify the billing phone matches the user's GCash number
        $billingPhone = $lastPayment['attributes']['billing']['phone'] ?? null;
        $paymentAmount = $lastPayment['attributes']['amount'] ?? 0;

        return new VerificationResult(
            success: true,
            referenceId: $referenceId,
            message: 'Account verified successfully!',
            amountCharged: $paymentAmount > 0 ? (int) $paymentAmount : $amountCentavos,
            metadata: [
                'payment_id' => $lastPayment['id'] ?? null,
                'phone' => $billingPhone,
            ],
        );
    }

    public function label(): string
    {
        return 'GCash (via PayMongo)';
    }

    public function key(): string
    {
        return 'paymongo';
    }

    private function validateNumber(string $number): void
    {
        if (!preg_match('/^09\d{9}$/', $number)) {
            throw new PaymentException('Invalid mobile number. Must be an 11-digit Philippine number starting with 09.');
        }
    }
}
