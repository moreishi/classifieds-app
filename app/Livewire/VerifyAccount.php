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
    public string $confirmAmount = '';
    public string $step = ''; // '' | 'number' | 'confirm' | 'done'

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
        $this->message = 'Number saved. Start verification to confirm ownership.';
    }

    public function startVerification(VerificationService $verificationService, PaymentGateway $gateway): void
    {
        $this->error = '';
        $this->message = '';

        try {
            $result = $verificationService->startVerification(auth()->user(), $gateway);
            $this->referenceId = $result['reference_id'];
            $this->hasPending = true;
            $this->message = $result['message'];
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function confirmVerification(VerificationService $verificationService, PaymentGateway $gateway): void
    {
        $this->validate([
            'confirmAmount' => 'required|numeric|min:1',
        ]);

        $this->error = '';

        try {
            $entered = (int) (round((float) $this->confirmAmount, 2) * 100);
            $verificationService->confirmVerification(auth()->user(), $gateway, $entered);
            $this->isVerified = true;
            $this->step = 'done';
            $this->message = 'Account verified successfully! Your listings now have a verified badge.';
            $this->dispatch('verification-complete');
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.verify-account');
    }
}
