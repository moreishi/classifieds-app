<?php

namespace App\Services\Payment;

class ChargeResult
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $referenceId = '',
        public readonly string $redirectUrl = '',
        public readonly string $message = '',
        public readonly float  $feeCentavos = 0,
    ) {}
}
