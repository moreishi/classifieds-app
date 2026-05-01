<?php

namespace App\Services\Payment;

class VerificationResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $referenceId,
        public readonly string $message,
        public readonly ?int $amountCharged = null,
        public readonly ?array $metadata = [],
    ) {}
}
