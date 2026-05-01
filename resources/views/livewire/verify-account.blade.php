<div>
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center gap-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Account Verification</h3>
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
            </div>
        @elseif($step === 'confirm')
            {{-- Start verification --}}
            @if(!$hasPending)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800 font-medium">Verify your GCash account</p>
                    <p class="text-xs text-blue-600 mt-1">
                        A small charge of <strong>₱1.00</strong> will be sent to <strong>{{ $gcashNumber }}</strong>.
                        Enter the exact amount to confirm ownership.
                    </p>
                </div>

                <button wire:click="startVerification"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Send Verification Charge
                </button>

            @else
                {{-- Enter amount --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-800 font-medium">Check your GCash inbox</p>
                    <p class="text-xs text-yellow-600 mt-1">
                        A charge of <strong>₱1.00</strong> was sent to <strong>{{ $gcashNumber }}</strong>.
                        Enter the exact amount you received to verify.
                    </p>
                </div>

                <div class="flex gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount Received (₱)</label>
                        <input type="text" wire:model="confirmAmount" inputmode="decimal"
                               class="block w-32 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                               placeholder="1.00" />
                    </div>
                    <button wire:click="confirmVerification"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        Confirm
                    </button>
                </div>
            @endif

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
