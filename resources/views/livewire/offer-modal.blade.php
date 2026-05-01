<div>
    @if($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             wire:click.self="close">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Send Offer</h2>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Your offer (₱)
                            <span class="text-xs text-gray-400 ml-2">Listed: ₱{{ number_format($listingPrice / 100) }}</span>
                        </label>
                        <input type="number" wire:model="amount" min="1"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg font-bold" />
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message (optional)</label>
                        <textarea wire:model="message" rows="3" maxlength="500"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Ask a question or add context..."></textarea>
                        @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button wire:click="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        Send Offer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
