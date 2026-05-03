<div>
    {{-- Page Header --}}
    <x-slot:title>Buy Credits — Iskina.ph</x-slot:title>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Buy Listing Credits</h1>
        <p class="text-gray-500 text-sm mb-8">
            Use credits to promote your listings and get more buyers. 
            Current balance: <strong class="text-blue-600">₱{{ number_format($balance / 100, 2) }}</strong>
        </p>

        @if($message)
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ $message }}
            </div>
        @endif

        @if($error)
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ $error }}
            </div>
        @endif

        @if(!$hasGcashNumber)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm mb-6">
                You need to <a href="{{ route('verify-account') }}" class="underline font-medium">verify your GCash account</a> first before buying credits.
            </div>
        @endif

        {{-- Credit Packs --}}
        <div class="grid gap-4 mb-8">
            @foreach($packs as $key => $pack)
                <label class="flex items-center gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all duration-150
                      {{ $selectedPack === $key ? 'border-blue-500 bg-blue-50 shadow-sm' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50' }}">
                    <input type="radio" wire:model.live="selectedPack" value="{{ $key }}"
                           class="w-5 h-5 text-blue-600 focus:ring-blue-500" />

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900">
                                ₱{{ number_format($pack['cents'] / 100, 2) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ number_format($pack['credits'] / 100) }} credits
                            </span>
                        </div>
                        @if(($pack['bonus'] ?? 0) > 0)
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                    </svg>
                                    +{{ number_format($pack['bonus'] / 100) }} bonus credits
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="text-right shrink-0">
                        @if(($pack['bonus'] ?? 0) > 0)
                            <span class="text-xs text-emerald-600 font-medium">Best value</span>
                        @endif
                    </div>
                </label>
            @endforeach
        </div>

        {{-- GCash Number (if not saved) --}}
        @if(!$hasGcashNumber)
            <div class="mb-6">
                <label for="gcashNumber" class="block text-sm font-medium text-gray-700 mb-1">GCash Number</label>
                <input type="tel" wire:model="gcashNumber" id="gcashNumber"
                       class="block w-full max-w-xs rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="09171234567" />
                <p class="text-xs text-gray-500 mt-1">We'll save this to your account.</p>
            </div>
        @endif

        {{-- Pay Button --}}
        <button wire:click="buy" wire:loading.attr="disabled"
                class="w-full max-w-xs bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                {{ !$hasGcashNumber ? 'disabled' : '' }}>
            <span wire:loading.remove>Pay with GCash</span>
            <span wire:loading>Processing...</span>
        </button>

        <p class="text-xs text-gray-400 mt-3">
            Powered by PayMongo. You'll be redirected to GCash to complete payment.
        </p>
    </div>
</div>
