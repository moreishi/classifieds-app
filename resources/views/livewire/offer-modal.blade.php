<div>
    {{-- Modal backdrop --}
    @if($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             x-on:click.away="window.Livewire.dispatch('close-offer-modal')">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Send Offer</h2>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Your offer (₱)</label>
                        <input type="number" wire:model="amount" min="1"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg font-bold"/>
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message (optional)</label>
                        <textarea wire:model="message" rows="3" maxlength="500"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Ask a question or add context..."></textarea>
                    </div>

                    <button wire:click="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        Send Offer
                    </button>
                </div>

                {{-- Toast notification placeholder --}
                <div x-data="{ show: false, message: '' }"
                     x-on:offer-sent.window="show = true; message = 'Offer sent!'; setTimeout(() => show = false, 3000)"
                     x-show="show"
                     x-transition
                     class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium">
                    <span x-text="message"></span>
                </div>
            </div>
        </div>
    @endif
</div>
