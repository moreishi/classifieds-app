<div>
    <div class="mt-4 pt-4 border-t">
        <button wire:click="$toggle('showForm')" class="text-xs text-gray-400 hover:text-red-500 transition-colors">
            ⚑ Report this listing
        </button>

        @if($showForm)
            <div class="mt-3 bg-gray-50 rounded-xl border p-4">
                <h4 class="font-medium text-sm text-gray-900 mb-3">Why are you reporting this?</h4>

                <form wire:submit="submit" class="space-y-3">
                    <div class="space-y-2">
                        @foreach($reasons as $value => $label)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" wire:model="reason" value="{{ $value }}"
                                       class="text-blue-600 focus:ring-blue-500" />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('reason')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <div>
                        <textarea wire:model="description" rows="2"
                                  placeholder="Any additional details (optional)..."
                                  class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('description')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                            Submit Report
                        </button>
                        <button type="button" wire:click="$set('showForm', false)"
                                class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    {{-- Toast events --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('report-submitted', () => {
                alert('Report submitted. Our team will review it shortly.');
            });
            @this.on('report-error', (event) => {
                alert(event.message);
            });
            @this.on('report-needs-login', () => {
                alert('You need to be logged in to report a listing.');
            });
        });
    </script>
</div>
