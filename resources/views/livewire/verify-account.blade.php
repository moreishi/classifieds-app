<div>
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center gap-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">GCash Verification</h3>
            @if($isVerified)
                <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-medium">
                    <span>&#10003;</span> Verified
                </span>
            @else
                <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-medium">
                    Unverified
                </span>
            @endif
        </div>

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

        @if($step === 'done' && $isVerified)
            <div class="text-sm text-gray-600 space-y-1">
                <p><span class="font-medium">Number:</span> {{ substr($gcashNumber, 0, 5) }}******</p>
                <p><span class="font-medium">Verified at:</span> {{ auth()->user()->gcash_verified_at->format('M d, Y h:i A') }}</p>
                <p class="text-green-600 mt-2">Your listings will now show a verified badge to buyers.</p>

                {{-- Upsell: Top up to ₱50 for listing credits --}}
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm font-medium text-blue-800">Want ₱50 in listing credits?</p>
                    <p class="text-xs text-blue-600 mt-1">
                        Just pay ₱45 more (total ₱50) to get credits you can use to bump your listings and get more buyers.
                    </p>
                    <button class="mt-2 bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">
                        Top Up ₱45
                    </button>
                </div>
            </div>

        @elseif($step === 'redirecting')
            <div class="text-center py-6" wire:poll.3s="checkVerificationStatus">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-sm text-gray-600">
                    Waiting for payment confirmation...
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Complete your GCash payment via the redirected page, then wait here.
                </p>
            </div>

            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('redirect-to-checkout', ({ url }) => {
                        if (url) window.location.href = url;
                    });
                });
            </script>

        @elseif($step === 'confirm')
            {{-- Start verification --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800 font-medium">Verify your GCash account</p>
                <p class="text-xs text-blue-600 mt-1">
                    Pay <strong>₱5.00</strong> via GCash to verify <strong>{{ $gcashNumber }}</strong>.
                    This confirms you own this number. The full ₱5 goes to PayMongo fees (₱0.11 only).
                </p>
            </div>

            <button wire:click="startVerification"
                    class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Pay ₱5 to Verify
            </button>

        @elseif($step === '')
            {{-- Enter number --}}
            <p class="text-sm text-gray-600 mb-3">
                Link your GCash account to get a verified badge on your listings.
                Your number will be kept private.
            </p>

            <div class="flex gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GCash Number</label>
                    <input type="tel" wire:model="gcashNumber" maxlength="11" inputmode="numeric"
                           class="block w-64 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="09171234567" />
                    @error('gcashNumber') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button wire:click="saveNumber"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Save & Continue
                </button>
            </div>
        @endif
    </div>
</div>
