<div>
    {{-- Header --}}
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            @isset($category)
                {{-- Category browsing --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-4xl">{{ $category->icon }}</span>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                        <p class="text-gray-500">Post price: ₱{{ number_format($category->post_price / 100) }}</p>
                    </div>
                </div>
                <div class="relative max-w-xl">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search in {{ $category->name }}..."
                        class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                </div>
            @else
                {{-- Search results --}}
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    @if(strlen($searchTerm) >= 2)
                        Results for "{{ $searchTerm }}"
                    @else
                        Search Iskina.ph
                    @endif
                </h1>
                <div class="relative max-w-xl">
                    <form wire:submit.prevent="render" class="relative">
                        <input
                            type="text"
                            wire:model="q"
                            placeholder="Search gadgets, cars, rooms in Cebu..."
                            class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </form>
                </div>
            @endisset
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Filters sidebar --}}
            <aside class="lg:w-64 shrink-0">
                <div class="bg-white rounded-xl border p-4 space-y-5">
                    <h3 class="font-semibold text-gray-900">Filters</h3>

                    <div>
                        <label class="text-sm font-medium text-gray-700">City</label>
                        <select wire:model.live="citySlug"
                                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->slug }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Price range (₱)</label>
                        <div class="flex gap-2 mt-1">
                            <input type="number" wire:model.live="minPrice" placeholder="Min"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500"/>
                            <input type="number" wire:model.live="maxPrice" placeholder="Max"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Condition</label>
                        <select wire:model.live="condition"
                                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500">
                            <option value="">All</option>
                            <option value="brand_new">Brand New</option>
                            <option value="like_new">Like New</option>
                            <option value="used">Used</option>
                            <option value="for_parts">For Parts</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Sort by</label>
                        <select wire:model.live="sort"
                                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500">
                            <option value="newest">Newest first</option>
                            <option value="oldest">Oldest first</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-500 mb-4">{{ $listings->total() }} listing(s) found</p>

                @if($listings->isEmpty())
                    <div class="text-center py-16 bg-gray-50 rounded-xl">
                        <p class="text-gray-500 text-lg">No listings found</p>
                        <p class="text-gray-400 text-sm mt-1">Try adjusting your filters or search terms</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($listings as $listing)
                            <a href="{{ route('listing.show', $listing->slug) }}"
                               class="bg-white rounded-xl border hover:shadow-md transition-shadow overflow-hidden">
                                <div class="h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                    No photo
                                </div>
                                <div class="p-3">
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $listing->title }}</h3>
                                    <p class="text-lg font-bold text-blue-600">₱{{ number_format($listing->price / 100) }}</p>
                                    <div class="flex items-center justify-between mt-1 text-xs text-gray-500">
                                        <span>📍 {{ $listing->city->name }}</span>
                                        <span>{{ $listing->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
