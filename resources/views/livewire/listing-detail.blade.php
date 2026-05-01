<div>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Photo gallery --}}
                @php $media = $listing->getMedia('photos'); @endphp
                @if($media->count() > 0)
                    <div x-data="{ active: 0, images: [
                        @foreach($media as $img)
                            { card: '{{ $img->getUrl('card') ?: $img->getUrl() }}', thumb: '{{ $img->getUrl('thumb') ?: $img->getUrl() }}' },
                        @endforeach
                    ]}" class="space-y-2">
                        {{-- Main Image --}}
                        <div class="bg-gray-100 rounded-xl overflow-hidden">
                            <img x-bind:src="images[active].card"
                                 alt="{{ $listing->title }}"
                                 class="w-full h-80 sm:h-96 object-contain" />
                        </div>
                        {{-- Thumbnail strip --}}
                        @if($media->count() > 1)
                            <div class="flex gap-2 overflow-x-auto pb-1">
                                <template x-for="(img, i) in images" :key="i">
                                    <button x-on:click="active = i"
                                            class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 focus:outline-none"
                                            x-bind:class="active === i ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-400'">
                                        <img x-bind:src="img.thumb" class="w-full h-full object-cover" />
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-100 rounded-xl h-80 flex items-center justify-center text-gray-400">
                        <span class="text-lg">No photos yet</span>
                    </div>
                @endif

                {{-- Title & Price --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $listing->title }}</h1>
                    <p class="text-3xl font-bold text-blue-600 mt-2">₱{{ number_format($listing->price / 100) }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>📍 {{ $listing->city->name }}</span>
                        <span>📂 {{ $listing->category->name }}</span>
                        @if($listing->condition)
                            <span>🔧 {{ str_replace('_', ' ', ucfirst($listing->condition)) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                <div class="prose max-w-none">
                    <h2 class="text-lg font-semibold text-gray-900">Description</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $listing->description }}</p>
                </div>

                {{-- Offer history --}}
                @if($listing->offers->count() > 0)
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Offers</h2>
                        <p class="text-sm text-gray-500">{{ $listing->offers->count() }} offer(s) received</p>
                    </div>
                @endif

                {{-- Reviews --}}
                @if($listing->reviews->count() > 0)
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Reviews</h2>
                        @foreach($listing->reviews as $review)
                            <div class="border-b py-3">
                                <div class="flex items-center gap-1 text-yellow-400">
                                    @for($i = 0; $i < $review->rating; $i++)⭐@endfor
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-700 text-sm mt-1">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-4">
                {{-- Seller Card --}}
                <div class="bg-white rounded-xl border p-5">
                    <h3 class="font-semibold text-gray-900">Seller</h3>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr($seller->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 flex items-center gap-1">
                                {{ $seller->name }}
                                @if($seller->gcash_verified_at)
                                    <span title="GCash Verified" class="inline-flex items-center justify-center w-4 h-4 bg-green-500 text-white rounded-full text-[10px] font-bold">&#10003;</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">
                                @switch($seller->reputation_tier)
                                    @case('pro') 🏆 Pro @break
                                    @case('trusted') ✅ Trusted @break
                                    @case('regular') 🔵 Regular @break
                                    @default ○ Newbie
                                @endswitch
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-500">
                        <p>Member since {{ $seller->created_at->format('M Y') }}</p>
                        @if($seller->reputation_points > 0)
                            <p>{{ $seller->reputation_points }} reputation points</p>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                @auth
                    <button
                        wire:click="$dispatch('openOfferModal', { listingId: {{ $listing->id }} })"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Send Offer
                    </button>

                    @if(auth()->id() === $listing->user_id)
                        <button
                            wire:click="markAsSold"
                            class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors"
                        >
                            Mark as Sold
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        Log in to send offer
                    </a>
                @endauth
            </aside>
        </div>
    </div>

    @auth
        <livewire:offer-modal />
    @endauth
</div>
