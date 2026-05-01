<div>
    @if(!$submitted)
        <div class="bg-white rounded-xl border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Leave a Review</h3>
            <p class="text-sm text-gray-600 mb-4">
                How was your experience buying <strong>{{ $receipt->listing->title }}</strong>?
            </p>

            <form wire:submit="submit" class="space-y-4">
                {{-- Star Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex gap-1" x-data="{ rating: @entangle('rating') }">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    x-on:click="rating = {{ $i }}"
                                    class="text-3xl transition-colors hover:scale-110"
                                    :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                &#9733;
                            </button>
                        @endfor
                    </div>
                    @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Comment --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comment (optional)</label>
                    <textarea wire:model="comment" rows="3" maxlength="1000"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Share your experience..."></textarea>
                    @error('comment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Submit Review
                </button>
            </form>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
            <p class="text-green-800 font-semibold text-lg">&#10003; Review Submitted</p>
            <p class="text-green-600 text-sm mt-1">Thanks for helping the community!</p>
        </div>
    @endif
</div>
