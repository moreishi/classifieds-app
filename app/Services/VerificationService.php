<?php

namespace App\Services;

use App\Models\User;
use App\Services\Payment\PaymentException;
use App\Services\Payment\PaymentGateway;

class VerificationService
{
    const int VERIFICATION_CHARGE_CENTAVOS = 500; // ₱5.00

    /**
     * Start the verification process for a user.
     * Creates a PayMongo Payment Intent and returns the checkout URL.
     *
     * @return array{reference_id: string, message: string, checkout_url: ?string}
     */
    public function startVerification(User $user, PaymentGateway $gateway): array
    {
        $accountId = $user->gcash_number;

        if (!$accountId) {
            throw new \RuntimeException('No GCash number set. Save your number first.');
        }

        if ($user->gcash_verified_at) {
            throw new \RuntimeException('Your account is already verified.');
        }

        $result = $gateway->chargeForVerification(
            accountId: $accountId,
            amountCentavos: self::VERIFICATION_CHARGE_CENTAVOS,
            metadata: ['user_id' => $user->id, 'email' => $user->email],
        );

        // Store pending verification
        cache()->put(
            "verification:{$user->id}",
            [
                'gateway' => $gateway->key(),
                'reference_id' => $result->referenceId,
                'amount_charged' => $result->amountCharged,
                'checkout_url' => $result->metadata['checkout_url'] ?? null,
                'started_at' => now(),
            ],
            now()->addHours(2),
        );

        return [
            'reference_id' => $result->referenceId,
            'message' => $result->message,
            'checkout_url' => $result->metadata['checkout_url'] ?? null,
        ];
    }

    /**
     * Confirm verification by checking payment status via the gateway.
     */
    public function confirmVerification(User $user, PaymentGateway $gateway, int $enteredAmountCentavos): void
    {
        $pending = cache()->get("verification:{$user->id}");

        if (!$pending) {
            throw new \RuntimeException('No pending verification found. Start the process again.');
        }

        if ($pending['gateway'] !== $gateway->key()) {
            throw new \RuntimeException('Verification gateway mismatch. Start again.');
        }

        $result = $gateway->confirmVerification(
            referenceId: $pending['reference_id'],
            amountCentavos: $pending['amount_charged'],
        );

        if (!$result->success) {
            throw new PaymentException('Verification failed: ' . $result->message);
        }

        $user->update([
            'gcash_verified_at' => now(),
        ]);

        cache()->forget("verification:{$user->id}");
    }

    /**
     * Check if a user is verified.
     */
    public function isVerified(User $user): bool
    {
        return !is_null($user->gcash_verified_at);
    }

    /**
     * Check if a user has a pending verification.
     */
    public function hasPendingVerification(User $user): bool
    {
        return cache()->has("verification:{$user->id}");
    }

    /**
     * Get pending verification details.
     */
    public function getPendingVerification(User $user): ?array
    {
        return cache()->get("verification:{$user->id}");
    }
}
