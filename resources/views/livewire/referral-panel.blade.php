<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Referral Program</h2>
        <span class="text-xs text-gray-400">Share &amp; earn</span>
    </div>

    {{-- Referral Link --}}
    <div class="px-5 py-4 border-b border-gray-100">
        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider block mb-2">Your Referral Link</label>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ $referralLink }}"
                   class="flex-1 rounded-lg border-gray-300 text-sm bg-gray-50 px-3 py-2 text-gray-800 font-mono text-xs"
                   onclick="this.select()"
                   id="referral-link-input" />
            <button onclick="navigator.clipboard.writeText(document.getElementById('referral-link-input').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shrink-0">
                Copy
            </button>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            Earn <strong>₱2</strong> per signup + <strong>₱5</strong> when they buy credits!
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 divide-x divide-gray-100">
        <div class="px-4 py-4 text-center">
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['invites_sent'] ?? 0) }}</p>
            <p class="text-xs text-gray-500">Invites Sent</p>
        </div>
        <div class="px-4 py-4 text-center">
            <p class="text-xl font-bold text-amber-600">{{ number_format($stats['pending_bonuses'] ?? 0) }}</p>
            <p class="text-xs text-gray-500">Pending Bonus</p>
        </div>
        <div class="px-4 py-4 text-center">
            <p class="text-xl font-bold text-green-600">₱{{ number_format(($stats['total_earned'] ?? 0) / 100) }}</p>
            <p class="text-xs text-gray-500">Total Earned</p>
        </div>
    </div>

    {{-- Recent Referrals --}}
    @if(! empty($recentReferrals))
        <div class="border-t border-gray-100">
            <div class="px-5 py-2 bg-gray-50/50 border-b border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Recent Referrals</p>
            </div>
            <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                @foreach($recentReferrals as $ref)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $ref['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $ref['signed_up_at']->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs font-medium {{ $ref['bonus_awarded'] ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $ref['bonus_awarded'] ? '₱2 earned' : 'Pending' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="px-5 py-6 text-center text-gray-400 border-t border-gray-100">
            <p class="text-sm">No referrals yet.</p>
            <p class="text-xs mt-1">Share your link to start earning!</p>
        </div>
    @endif
</div>
