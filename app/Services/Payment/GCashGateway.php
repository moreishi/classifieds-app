<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * GCash payment gateway implementation.
 *
 * In production, this calls the GCash/Mynt API.
 * For development, it simulates the flow using a random charge.
 *
 * To swap providers, create a new class implementing PaymentGateway
 * and update the binding in AppServiceProvider.
 */
class GCashGateway implements PaymentGateway
{
    /**
     * GCash numbers are 11-digit Philippine mobile numbers starting with 09.
     */
    public function verifyAccount(string $accountId, array $metadata = []): VerificationResult
    {
        $this->validateNumber($accountId);

        // In production: call GCash API to check if number is active
        // e.g. POST https://api.gcash.com/v1/accounts/verify
        // For dev: simulate success
        Log::info("[GCash] verifyAccount: {$accountId}");

        return new VerificationResult(
            success: true,
            referenceId: Str::random(16),
            message: 'Account is active.',
        );
    }

    /**
     * Charge a small amount (e.g. ₱1) to confirm the user owns this number.
     * The user enters the exact amount to verify ownership.
     */
    public function chargeForVerification(string $accountId, int $amountCentavos, array $metadata = []): VerificationResult
    {
        $this->validateNumber($accountId);

        // In production: POST https://api.gcash.com/v1/charges
        // {
        //   "mobile_number": $accountId,
        //   "amount": $amountCentavos / 100,
        //   "description": "Iskina.ph account verification"
        // }
        Log::info("[GCash] chargeForVerification: {$accountId}, amount: {$amountCentavos}");

        $reference = 'GC-VFY-' . strtoupper(Str::random(12));

        return new VerificationResult(
            success: true,
            referenceId: $reference,
            message: "A charge of ₱" . number_format($amountCentavos / 100, 2) . " has been sent. Enter the exact amount to verify.",
            amountCharged: $amountCentavos,
        );
    }

    /**
     * Confirm verification by matching the charged amount.
     * In production, you'd call GCash API to check the payment status.
     */
    public function confirmVerification(string $referenceId, int $amountCentavos): VerificationResult
    {
        // In production: GET https://api.gcash.com/v1/charges/{referenceId}
        // Check if status === 'success' and amount matches
        Log::info("[GCash] confirmVerification: {$referenceId}, amount: {$amountCentavos}");

        return new VerificationResult(
            success: true,
            referenceId: $referenceId,
            message: 'Account verified successfully!',
            amountCharged: $amountCentavos,
        );
    }

    public function label(): string
    {
        return 'GCash';
    }

    public function key(): string
    {
        return 'gcash';
    }

    private function validateNumber(string $number): void
    {
        if (!preg_match('/^09\d{9}$/', $number)) {
            throw new PaymentException('Invalid GCash number. Must be an 11-digit Philippine mobile number starting with 09.');
        }
    }
}
