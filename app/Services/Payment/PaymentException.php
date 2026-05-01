<?php

namespace App\Services\Payment;

class PaymentException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?string $gatewayReference = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
