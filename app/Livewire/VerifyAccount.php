<?php

namespace App\Livewire;

use App\Services\Payment\PaymentGateway;
use App\Services\VerificationService;
use Livewire\Component;

class VerifyAccount extends Component
{
    public string $gcashNumber = '';
    public bool $isVerified = false;
    public bool $hasPending = false;
    public ?string $referenceId = null;
    public string $message = '';
    public string $error = '';
    public string $step = ''; // '' | 'number' | 'redirecting' | 'done'

    public function mount(): void
    {
        $user = auth()->user();
        $this->gcashNumber = $user->gcash_number ?? '';
        $this->isVerified = !is_null($user->gcash_verified_at);

        if ($this->isVerified) {
            $this->step = 'done';
        }
    }

    public function saveNumber(): void
    {
        $this->validate([
            'gcashNumber' => ['required', 'regex:/^09\d{9}$/', 'size:11'],
        ]);

        auth()->user()->update(['gcash_number' => $this->gcashNumber]);
        $this->step = 'confirm';
        $this->message = 'Number saved. Now verify with a ₱5 GCash payment.';
    }

    public function startVerification(VerificationService $verificationService, PaymentGateway $gateway): void
    {
        $this->error = '';
        $this->message = '';

        try {
            $result = $verificationService->startVerification(auth()->user(), $gateway);
            $this->referenceId = $result['reference_id'];
            $this->hasPending = true;

            $this->dispatch('redirect-to-checkout', url: $result['checkout_url']);
            $this->step = 'redirecting';
            $this->message = 'Redirecting to GCash... Complete the payment to verify.';
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function checkVerificationStatus(): void
    {
        $user = auth()->user();
        $this->isVerified = !is_null($user->gcash_verified_at);

        if ($this->isVerified) {
            $this->step = 'done';
            $this->message = 'Account verified successfully! Your listings will now show a verified badge.';
            $this->dispatch('verification-complete');
        }
    }

    public function render()
    {
        return view('livewire.verify-account');
    }
}
