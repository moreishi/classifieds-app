<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BuyCredits extends Component
{
    public const array CREDIT_PACKS = [
        'basic'  => ['cents' => 5000,  'credits' => 5000,  'label' => '₱50  —  50 Credits'],
        'mid'    => ['cents' => 10000, 'credits' => 10000, 'label' => '₱100 — 100 Credits'],
        'boost'  => ['cents' => 20000, 'credits' => 20000, 'label' => '₱200 — 200 Credits (+20 bonus!)', 'bonus' => 2000],
        'pro'    => ['cents' => 50000, 'credits' => 50000, 'label' => '₱500 — 500 Credits (+100 bonus!)', 'bonus' => 10000],
    ];

    public string $selectedPack = 'basic';
    public string $gcashNumber = '';
    public ?string $redirectUrl = null;
    public ?string $message = null;
    public ?string $error = null;
    public bool $submitting = false;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user->gcash_number) {
            $this->gcashNumber = $user->gcash_number;
        }
    }

    public function buy(): void
    {
        $this->reset(['message', 'error']);

        if ($this->submitting) {
            return;
        }

        $user = Auth::user();

        if (!isset(self::CREDIT_PACKS[$this->selectedPack])) {
            $this->error = 'Invalid pack selected.';
            return;
        }

        $pack = self::CREDIT_PACKS[$this->selectedPack];

        if (!$user->gcash_number) {
            $this->error = 'Please save your GCash number first.';
            return;
        }

        $this->submitting = true;

        try {
            /** @var PaymentGateway $gateway */
            $gateway = app(PaymentGateway::class);

            // Charge the user via PayMongo
            $result = $gateway->charge(
                phoneNumber: $this->gcashNumber,
                amountCentavos: $pack['cents'],
                description: "Buy {$pack['credits']} listing credits",
                metadata: [
                    'user_id' => $user->id,
                    'type' => 'buy_credits',
                    'pack' => $this->selectedPack,
                    'credits' => $pack['credits'],
                    'bonus' => $pack['bonus'] ?? 0,
                ],
            );

            if (!$result->success) {
                $this->error = 'Payment failed: ' . $result->message;
                $this->submitting = false;
                return;
            }

            if ($result->redirectUrl) {
                // Store pending purchase in cache
                cache()->put("purchase:{$user->id}", [
                    'gateway' => $gateway->key(),
                    'reference_id' => $result->referenceId,
                    'pack' => $this->selectedPack,
                    'credits' => $pack['credits'],
                    'bonus' => $pack['bonus'] ?? 0,
                    'amount' => $pack['cents'],
                    'gcash_number' => $this->gcashNumber,
                ], now()->addHours(2));

                $this->redirectUrl = $result->redirectUrl;
                $this->dispatch('redirect-to-checkout', url: $result->redirectUrl);
            }
        } catch (\Throwable $e) {
            $this->error = 'Something went wrong. Please try again.';
            logger()->error('Buy credits failed', [
                'user' => $user->id,
                'pack' => $this->selectedPack,
                'error' => $e->getMessage(),
            ]);
        }

        $this->submitting = false;
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.buy-credits', [
            'packs' => self::CREDIT_PACKS,
            'balance' => $user ? $user->credit_balance : 0,
            'hasGcashNumber' => $user && $user->gcash_number,
        ])->layout('layouts.app');
    }
}
