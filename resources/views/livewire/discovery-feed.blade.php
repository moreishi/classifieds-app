@push('head')
    <x-seo
        title="Ganaps — Discover Local Events & Pop-Ups | Iskina.ph"
        description="Find the best local events, pop-ups, and ganaps in Cebu. From night markets to art jams, discover what's happening near you."
        :url="route('ganaps.index')"
    />
@endpush

<div>
    {{-- Hero --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-20 lg:py-24 text-center">
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-none drop-shadow-lg">
                Ganaps
            </h1>
            <p class="mt-4 text-lg sm:text-xl text-white/80 max-w-lg mx-auto font-medium drop-shadow">
                Discover local events, pop-ups, and happenings around you.
            </p>
            <p class="mt-1 text-sm text-white/60">Tagalog slang for <span class="italic">"things to do"</span></p>
        </div>
    </div>

    {{-- Live Now section --}}
    @if($liveBeacons->isNotEmpty())
        <div class="bg-white border-b border-gray-100" wire:poll.30s>
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-pink opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-neon-pink"></span>
                    </span>
                    <h2 class="text-lg font-bold text-gray-900">Live Now</h2>
                    <span class="text-xs text-gray-400">{{ $liveBeacons->count() }} selling</span>
                </div>
                <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory scrollbar-hide -mx-4 px-4">
                    @foreach($liveBeacons as $beacon)
                        <div wire:key="beacon-{{ $beacon->id }}"
                             class="snap-start flex-shrink-0 w-44 group relative rounded-2xl overflow-hidden bg-gray-900 shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer">

                            {{-- Snapshot --}}
                            <div class="aspect-[3/4] overflow-hidden">
                                @if($beacon->getFirstMediaUrl('snapshot', 'thumb'))
                                    <img src="{{ $beacon->getFirstMediaUrl('snapshot', 'thumb') }}"
                                         alt="Live selling"
                                         class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                        <span class="text-4xl">📸</span>
                                    </div>
                                @endif
                            </div>

                            {{-- LIVE badge --}}
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-neon-pink text-white text-[10px] font-bold uppercase tracking-wider shadow-lg shadow-neon-pink/40">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                    </span>
                                    LIVE
                                </span>
                            </div>

                            {{-- Bottom overlay --}}
                            <div class="absolute bottom-0 left-0 right-0">
                                <div class="h-28 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-3">
                                    <p class="text-white text-xs font-bold leading-tight line-clamp-2 drop-shadow-lg">
                                        {{ $beacon->description }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <img src="{{ $beacon->user->avatar }}" alt="" class="w-4 h-4 rounded-full flex-shrink-0">
                                        <span class="text-[11px] text-gray-300 truncate">{{ $beacon->user->publicName() }}</span>
                                    </div>
                                    @if($beacon->location_name)
                                        <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $beacon->location_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Vibe filter bar --}}
    <div class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent"
                 x-data="{ active: @entangle('vibe') }"
                 x-on:keydown.right.prevent="$event.target.nextElementSibling?.focus()"
                 x-on:keydown.left.prevent="$event.target.previousElementSibling?.focus()">
                <button wire:click="$set('vibe', '')"
                        x-on:click="active = ''"
                        :class="active === '' ? 'bg-gray-900 text-white ring-2 ring-gray-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                        class="shrink-0 px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    All Ganaps
                </button>

                @php
                    $vibeIcons = [
                        'Party' => '⚡',
                        'Hustle' => '💼',
                        'Art' => '🎨',
                        'Tech' => '💻',
                        'Music' => '🎵',
                        'Food' => '🍕',
                        'Sports' => '🏀',
                        'Community' => '🤝',
                    ];
                    $vibeColors = [
                        'Party' => 'ring-neon-pink text-neon-pink border-neon-pink/30 bg-neon-pink/10',
                        'Hustle' => 'ring-neon-green text-emerald-600 border-emerald-300 bg-emerald-50',
                        'Art' => 'ring-neon-blue text-blue-600 border-blue-300 bg-blue-50',
                        'Tech' => 'ring-neon-purple text-purple-600 border-purple-300 bg-purple-50',
                        'Music' => 'ring-yellow-400 text-yellow-600 border-yellow-300 bg-yellow-50',
                        'Food' => 'ring-neon-orange text-orange-600 border-orange-300 bg-orange-50',
                        'Sports' => 'ring-neon-lime text-lime-600 border-lime-300 bg-lime-50',
                        'Community' => 'ring-neon-cyan text-cyan-600 border-cyan-300 bg-cyan-50',
                    ];
                    $vibeGlow = [
                        'Party' => 'shadow-neon-pink/25',
                        'Hustle' => 'shadow-emerald-400/25',
                        'Art' => 'shadow-blue-400/25',
                        'Tech' => 'shadow-purple-400/25',
                        'Music' => 'shadow-yellow-400/25',
                        'Food' => 'shadow-orange-400/25',
                        'Sports' => 'shadow-lime-400/25',
                        'Community' => 'shadow-cyan-400/25',
                    ];
                @endphp

                @foreach($vibes as $vibeName)
                    <button wire:click="$set('vibe', '{{ $vibeName }}')"
                            x-on:click="active = '{{ $vibeName }}'"
                            :class="active === '{{ $vibeName }}' ? 'ring-2 {{ $vibeColors[$vibeName] }} shadow-lg {{ $vibeGlow[$vibeName] }}' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 border border-transparent'"
                            class="shrink-0 px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 {{ str_replace('ring-', 'focus:ring-', explode(' ', $vibeColors[$vibeName])[0]) }}">
                        <span class="mr-1.5">{{ $vibeIcons[$vibeName] }}</span>
                        {{ $vibeName }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Feed Grid --}}
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 py-8">
            @if($events->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <span class="text-6xl mb-4">🔍</span>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No ganaps found</h3>
                    <p class="text-gray-500 max-w-sm">
                        @if($vibe)
                            No {{ $vibe }} events coming up. Try a different vibe.
                        @else
                            No upcoming events yet. Check back soon!
                        @endif
                    </p>
                    @if($vibe)
                        <button wire:click="$set('vibe', '')"
                                class="mt-6 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full text-sm font-semibold transition-colors">
                            Show all ganaps
                        </button>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 lg:gap-6">
                    @foreach($events as $event)
                        <div wire:key="event-{{ $event->id }}"
                             class="group relative rounded-2xl overflow-hidden bg-white shadow-md hover:shadow-xl transition-all duration-500 ease-out cursor-pointer
                                    active:scale-[0.98]"
                             x-data="{ showDescr: false }"
                             @click="window.location.href = '{{ route('ganaps.show', $event->slug) }}'"
                             @mouseenter="showDescr = true"
                             @mouseleave="showDescr = false">

                            {{-- Cover Image --}}
                            <div class="aspect-[3/4] sm:aspect-[4/5] overflow-hidden">
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200">
                                    @if($event->cover_image)
                                        <img src="{{ $event->cover_image }}"
                                             alt="{{ $event->title }}"
                                             class="w-full h-full object-cover transition-all duration-700 ease-out
                                                    group-hover:scale-110 group-hover:rotate-[1deg]"
                                             loading="lazy"
                                             onerror="this.parentElement.classList.add('hidden')" />
                                    @endif
                                </div>
                            </div>

                            {{-- Vibe Tag (top-right) --}}
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                             @switch($event->vibe)
                                                 @case('Party') bg-neon-pink/90 text-white shadow-lg shadow-neon-pink/30 @break
                                                 @case('Hustle') bg-emerald-500 text-white shadow-lg shadow-emerald-400/30 @break
                                                 @case('Art') bg-blue-500 text-white shadow-lg shadow-blue-400/30 @break
                                                 @case('Tech') bg-purple-500 text-white shadow-lg shadow-purple-400/30 @break
                                                 @case('Music') bg-yellow-500 text-white shadow-lg shadow-yellow-400/30 @break
                                                 @case('Food') bg-orange-500 text-white shadow-lg shadow-orange-400/30 @break
                                                 @case('Sports') bg-lime-500 text-white shadow-lg shadow-lime-400/30 @break
                                                 @case('Community') bg-cyan-500 text-white shadow-lg shadow-cyan-400/30 @break
                                                 @default bg-gray-500 text-white shadow-lg shadow-gray-400/30
                                             @endswitch">
                                    {{ $vibeIcons[$event->vibe] ?? '' }}
                                    {{ $event->vibe }}
                                </span>
                            </div>

                            {{-- Bottom gradient overlay + info --}}
                            <div class="absolute bottom-0 left-0 right-0">
                                <div class="h-32 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>

                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <h3 class="text-white font-bold text-base sm:text-lg leading-tight drop-shadow-lg">
                                        {{ $event->title }}
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-300">
                                        <span>{{ $event->event_date->format('M d, Y') }}</span>
                                        <span class="w-1 h-1 rounded-full bg-gray-500"></span>
                                        <span class="truncate">📍 {{ $event->location_name }}</span>
                                    </div>

                                    {{-- Description (reveals on desktop hover) --}}
                                    <div class="mt-2 overflow-hidden transition-all duration-500 ease-out"
                                         x-show="showDescr"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 translate-y-2">
                                        <p class="text-xs text-gray-200 leading-relaxed line-clamp-2">
                                            {{ $event->description }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Desktop hover: subtle border --}}
                            <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-black/5 group-hover:ring-blue-400/50 transition-all duration-500"></div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
