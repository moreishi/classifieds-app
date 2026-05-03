<?php

namespace App\Services\Payment;

interface PaymentGateway
{
    /**
     * Verify that a user owns the given account identifier.
     * Returns a VerificationResult on success.
     *
     * @throws PaymentException
     */
    public function verifyAccount(string $accountId, array $metadata = []): VerificationResult;

    /**
     * Charge a user a small amount (e.g. ₱1) to confirm account ownership.
     * The user must enter the exact amount to prove ownership.
     */
    public function chargeForVerification(string $accountId, int $amountCentavos, array $metadata = []): VerificationResult;

    /**
     * Confirm a verification by matching the amount charged.
     */
    public function confirmVerification(string $referenceId, int $amountCentavos): VerificationResult;

    /**
     * A generic charge for buying credits.
     *
     * @throws PaymentException
     */
    public function charge(string $phoneNumber, int $amountCentavos, string $description, array $metadata = []): ChargeResult;

    /**
     * Get a human-readable label for this gateway (e.g. "GCash", "PayMongo", "Maya").
     */
    public function label(): string;

    /**
     * Get the gateway identifier key (e.g. "gcash", "paymongo").
     */
    public function key(): string;
}
