<div>
    @if($message)
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-3 text-sm">
            {{ $message }}
        </div>
    @endif

    @if($error)
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg mb-3 text-sm">
            {{ $error }}
        </div>
    @endif

    @if($hasActivePromotion)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">
            This listing is currently promoted
            @if($this->listing->featured_until)
                until {{ $this->listing->featured_until->format('M d, Y') }}.
            @else
                .
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl border p-5">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Promote This Listing
            </h3>

            <p class="text-sm text-gray-500 mb-4">
                Get more buyers. Promoted listings appear at the top of search results.
            </p>

            <div class="flex flex-col gap-4">
                <div class="space-y-2">
                    @foreach($plans as $key => $plan)
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors
                              {{ $selectedPlan === $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model="selectedPlan" value="{{ $key }}"
                                   class="text-blue-600 focus:ring-blue-500" />
                            <div class="flex-1">
                                <span class="font-medium text-gray-900">{{ $plan['label'] }}</span>
                                <span class="text-sm text-gray-500 ml-2">₱{{ number_format($plan['price'] / 100, 2) }}</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ $plan['days'] }} days</span>
                        </label>
                    @endforeach
                </div>

                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Your balance: <strong>₱{{ number_format($balance / 100, 2) }}</strong></span>
                    <button wire:click="bump"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Pay &amp; Promote</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
