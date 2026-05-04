<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Credit Balance Card --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Credits</p>
                    <p class="text-2xl font-bold text-blue-600">₱{{ number_format(auth()->user()->credit_balance / 100) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Reputation</p>
                    <div class="mt-2">
                        <x-member-badge :user="auth()->user()" size="lg" :showPoints="true" />
                        <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                            <p>🏷️ Seller: {{ number_format(auth()->user()->reputation_points) }} pts</p>
                            <p>🛒 Buyer: {{ number_format(auth()->user()->buyer_points ?? 0) }} pts</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Free Listings</p>
                    @php $remaining = app(\App\Services\CreditService::class)->freeListingsRemaining(auth()->user()); @endphp
                    <p class="text-2xl font-bold text-gray-900">{{ $remaining }} / {{ \App\Services\CreditService::freeListingsLimit(auth()->user()->reputation_tier) }} remaining</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Referral Code</p>
                    <p class="text-lg font-mono font-bold text-green-600">{{ auth()->user()->referral_code ?? '—' }}</p>
                </div>
            </div>

            {{-- Referral Link --}}
            @if(auth()->user()->referral_code)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-2">Refer a Friend</h3>
                    <p class="text-sm text-gray-600 mb-3">Share your referral link and earn <strong>₱2</strong> per signup + <strong>₱5</strong> when they buy credits!</p>
                    <div class="flex gap-2">
                        <input type="text" readonly
                               value="{{ route('register', ['ref' => auth()->user()->referral_code]) }}"
                               class="flex-1 rounded-lg border-gray-300 text-sm bg-gray-50 px-3 py-2"
                               onclick="this.select()" />
                        <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copied!'"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                            Copy
                        </button>
                    </div>
                </div>
            @endif

            {{-- Recent Activity --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
