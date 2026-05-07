<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Credits</p>
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format(auth()->user()->credit_balance / 100) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Reputation</p>
                            <x-member-badge :user="auth()->user()" size="sm" :showPoints="false" />
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500 space-y-0.5">
                        <p>🏷️ Seller: {{ number_format(auth()->user()->reputation_points) }} pts</p>
                        <p>🛒 Buyer: {{ number_format(auth()->user()->buyer_points ?? 0) }} pts</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Free Listings</p>
                            @php $remaining = app(\App\Services\CreditService::class)->freeListingsRemaining(auth()->user()); @endphp
                            <p class="text-2xl font-bold text-gray-900">{{ $remaining }} <span class="text-sm font-normal text-gray-500">/ {{ \App\Services\CreditService::freeListingsLimit(auth()->user()->reputation_tier) }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Referral Code</p>
                            <p class="text-lg font-mono font-bold text-gray-900">{{ auth()->user()->referral_code ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Referral Link --}}
            @if(auth()->user()->referral_code)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-2">Refer a Friend</h3>
                    <p class="text-sm text-gray-600 mb-3">Share your referral link and earn <strong>₱2</strong> per signup + <strong>₱5</strong> when they buy credits!</p>
                    <div class="flex gap-2">
                        <input type="text" readonly
                               value="{{ route('register', ['ref' => auth()->user()->referral_code]) }}"
                               class="flex-1 rounded-lg border-gray-300 text-sm bg-gray-50 px-3 py-2"
                               onclick="this.select()" />
                        <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copied!'"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shrink-0">
                            Copy
                        </button>
                    </div>
                </div>
            @endif

            {{-- Recent Activity --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
