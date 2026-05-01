<div>
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Listings</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your active and sold listings.</p>
            </div>
            <a href="{{ route('listings.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                + New Listing
            </a>
        </div>

        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        @if($listings->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-lg">No listings yet</p>
                <p class="text-gray-400 text-sm mt-1">Create your first listing to get started!</p>
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

                        </div>

                        {{-- Details --}}
                        <div class="p-4">
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

                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                                <a href="{{ route('listing.show', $listing->slug) }}"
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('listings.edit', $listing->slug) }}"
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                <span class="text-xs text-gray-400">{{ $listing->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>
