<?php

namespace App\Livewire;

use App\Services\CreditService;
use Livewire\Component;

class ReferralPanel extends Component
{
    public string $referralLink = '';
    public array $stats = [];
    public array $recentReferrals = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $this->referralLink = route('register', ['ref' => $user->referral_code]);

        /** @var CreditService $credits */
        $credits = app(CreditService::class);

        $this->stats = $credits->getReferralStats($user);
        $this->recentReferrals = $credits->getRecentReferrals($user)->toArray();
    }

    public function render()
    {
        return view('livewire.referral-panel');
    }
}
