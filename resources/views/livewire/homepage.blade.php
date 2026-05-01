<div>
    {{-- Search Hero --}}
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-center mb-2">Find it in Cebu</h1>
            <p class="text-blue-100 text-center mb-8 text-lg">Search gadgets, cars, rooms, and more</p>

            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input
                        type="text"
                        wire:model="search"
                        wire:keydown.enter="searchListings"
                        placeholder="Search gadgets, cars, rooms in Cebu..."
                        class="w-full px-5 py-4 pr-12 rounded-xl text-gray-900 text-lg shadow-lg focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    />
                    <button wire:click="searchListings" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- City selector hint --}}
            <div class="flex justify-center mt-6 gap-2 text-sm text-blue-200">
                <span>📍</span>
                <span>Cebu City, Cebu, Philippines</span>
            </div>
        </div>
    </div>

    {{-- Category Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Browse by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                    <span class="text-4xl mb-3">{{ $category->icon }}</span>
                    <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Featured Listings --}}
    <div class="max-w-7xl mx-auto px-4 pb-16 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured from Iskina</h2>

        @if($featuredListings->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <p class="text-gray-500">No featured listings this week.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredListings as $listing)
                    <a href="{{ route('listing.show', $listing->slug) }}"
                       class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100">
                        @php $thumb = $listing->getFirstMediaUrl('photos', 'thumb') ?: $listing->getFirstMediaUrl('photos'); @endphp
                        @if($thumb)
                            <div class="h-48 bg-gray-200 overflow-hidden">
                                <img src="{{ $thumb }}" alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover" />
                            </div>
                        @else
                            <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                No photo
                            </div>
                        @endif
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $listing->title }}</h3>
                                @if($listing->status === 'sold')
                                    <span class="shrink-0 bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Sold</span>
                                @endif
                            </div>
                            @if($listing->status === 'sold')
                                <p class="text-lg font-bold text-gray-400 mt-1">
                                    <s>₱{{ number_format($listing->price / 100) }}</s>
                                </p>
                            @else
                                <p class="text-xl font-bold text-blue-600 mt-1">₱{{ number_format($listing->price / 100) }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-2 text-sm text-gray-500">
                                <span>📍 {{ $listing->city->name }}</span>
                                <span>{{ $listing->user->name }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
