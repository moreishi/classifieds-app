<div>
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-500" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                    </svg>
                    My Favorites
                </h1>
                <p class="text-sm text-gray-500 mt-1">Listings you've saved.</p>
            </div>
        </div>

        @if($listings->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
                <p class="text-gray-500 text-lg">No favorites yet</p>
                <p class="text-gray-400 text-sm mt-1">Browse listings and save your favorites!</p>
                <a href="{{ route('home') }}"
                   class="inline-block mt-4 bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                    Browse Listings
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($listings as $listing)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Photo --}}
                        <div class="h-40 bg-gray-100 relative">
                            @if($listing->getFirstMediaUrl('photos', 'card'))
                                <img src="{{ $listing->getFirstMediaUrl('photos', 'card') }}"
                                     alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover" />
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            {{-- Heart overlay --}}
                            <div class="absolute top-2 right-2 z-10" wire:key="favorite-{{ $listing->id }}" @click.stop>
                                <livewire:toggle-favorite :listing="$listing" />
                            </div>
                        </div>

                        {{-- Details --}}
                        <a href="{{ route('listing.show', $listing->slug) }}" class="block p-4">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $listing->title }}</h3>
                                @if($listing->status === 'sold')
                                    <span class="shrink-0 px-2 py-0.5 bg-red-600 text-white text-xs font-medium rounded-full">Sold</span>
                                @else
                                    <span class="shrink-0 px-2 py-0.5 bg-green-600 text-white text-xs font-medium rounded-full">Active</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mt-1">₱{{ number_format($listing->price / 100) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $listing->city?->name ?? '—' }}</p>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>
