<div>
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Listings</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your active and sold listings.</p>
            </div>
            <a href="{{ route('listings.create') }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New Listing
            </a>
        </div>

        @if(session('message'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
                {{ session('message') }}
            </div>
        @endif

        @if($listings->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No listings yet</p>
                <p class="text-gray-400 text-sm mt-1">Create your first listing to get started!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($listings as $listing)
                    <div class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        {{-- Photo --}}
                        <div class="aspect-[4/3] bg-gray-100 relative overflow-hidden">
                            @if($listing->getFirstMediaUrl('photos', 'card'))
                                <img src="{{ $listing->getFirstMediaUrl('photos', 'card') }}"
                                     alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            @else
                                <div class="flex items-center justify-center h-full text-gray-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                @if($listing->status === 'sold')
                                    <span class="px-2.5 py-1 bg-red-500 text-white text-xs font-semibold rounded-full shadow-sm">Sold</span>
                                @else
                                    <span class="px-2.5 py-1 bg-green-500 text-white text-xs font-semibold rounded-full shadow-sm">Active</span>
                                @endif
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $listing->title }}</h3>
                            <p class="text-lg font-bold text-blue-600 mt-1">₱{{ number_format($listing->price / 100) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $listing->city?->name ?? '—' }}</p>

                            <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 text-sm">
                                <a href="{{ route('listing.show', $listing->slug) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    View
                                </a>
                                <span class="text-gray-300">·</span>
                                <a href="{{ route('listings.edit', $listing->slug) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    Edit
                                </a>
                                <span class="text-gray-300">·</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $listing->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>
