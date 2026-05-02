@push('head')
    <x-seo
        title="Buy & Sell Locally in Cebu"
        description="The local marketplace for Cebu. Buy and sell gadgets, cars, property, jobs, services, and more near you."
        :url="route('home')"
    />
@endpush

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
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Browse by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="flex flex-col items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                    <span class="text-2xl mb-2">{{ $category->icon }}</span>
                    <span class="text-xs font-medium text-gray-700">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Featured Listings --}}
    <div class="max-w-7xl mx-auto px-4 pb-10 sm:px-6 lg:px-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Featured from Iskina</h2>

        @if($featuredListings->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <p class="text-gray-500">No featured listings this week.</p>
            </div>
        @else
            <div class="grid grid-cols-4 gap-3">
                @foreach($featuredListings as $listing)
                    <a href="{{ route('listing.show', $listing->slug) }}"
                       class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100">
                        @php $thumb = $listing->getFirstMediaUrl('photos', 'thumb') ?: $listing->getFirstMediaUrl('photos'); @endphp
                        @if($thumb)
                            <div class="h-28 bg-gray-200 overflow-hidden relative">
                                <img src="{{ $thumb }}" alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover" />
                                @auth
                                    <div class="absolute top-2 right-2 z-10" wire:key="favorite-{{ $listing->id }}" @click.stop>
                                        <livewire:toggle-favorite :listing="$listing" />
                                    </div>
                                @endauth
                            </div>
                        @else
                            <div class="h-28 bg-gray-200 flex items-center justify-center text-gray-400 relative">
                                No photo
                            </div>
                        @endif
                        <div class="p-2.5">
                            <h3 class="font-semibold text-xs text-gray-900 truncate">{{ $listing->title }}</h3>
                            @if($listing->status === 'sold')
                                <p class="text-sm font-bold text-gray-400 mt-0.5">
                                    <s>₱{{ number_format($listing->price / 100) }}</s>
                                </p>
                            @else
                                <p class="font-bold text-blue-600 mt-0.5">₱{{ number_format($listing->price / 100) }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-1 text-xs text-gray-500">
                                <span>📍 {{ $listing->city->name }}</span>
                                <span class="flex items-center gap-1">
                                    <img src="{{ $listing->user->avatar }}" alt="" class="w-4 h-4 rounded-full" />
                                    {{ $listing->user->name }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
