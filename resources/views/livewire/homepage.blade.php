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
                        wire:model.live="search"
                        placeholder="Search gadgets, cars, rooms in Cebu..."
                        class="w-full px-5 py-4 pr-12 rounded-xl text-gray-900 text-lg shadow-lg focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    />
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- City selector --}}
            <div class="flex justify-center mt-6 gap-2 text-sm text-blue-200">
                <span>📍</span>
                <select class="bg-blue-700 text-white rounded-lg px-3 py-1 border border-blue-500 focus:outline-none">
                    <option>Cebu City</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
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
                        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                            No photo
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $listing->title }}</h3>
                            <p class="text-xl font-bold text-blue-600 mt-1">₱{{ number_format($listing->price / 100) }}</p>
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
