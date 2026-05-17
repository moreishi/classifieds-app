@push('head')
    <x-seo
        :title="$event->title . ' — Ganaps | Iskina.ph'"
        :description="Str::limit($event->description, 160)"
        :url="route('ganaps.show', $event->slug)"
    />
@endpush

<div>
    {{-- Back link --}}
    <div class="max-w-4xl mx-auto px-4 pt-6">
        <a href="{{ route('ganaps.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <span>&larr;</span> Back to all ganaps
        </a>
    </div>

    {{-- Hero --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
        <div class="max-w-4xl mx-auto px-4 py-12 sm:py-16">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                            @switch($event->vibe)
                                @case('Party') bg-neon-pink/90 text-white @break
                                @case('Hustle') bg-emerald-500 text-white @break
                                @case('Art') bg-blue-500 text-white @break
                                @case('Tech') bg-purple-500 text-white @break
                                @case('Music') bg-yellow-500 text-white @break
                                @case('Food') bg-orange-500 text-white @break
                                @case('Sports') bg-lime-500 text-white @break
                                @case('Community') bg-cyan-500 text-white @break
                                @default bg-gray-500 text-white
                            @endswitch">
                            @php
                                $vibeIcons = [
                                    'Party' => '⚡', 'Hustle' => '💼', 'Art' => '🎨', 'Tech' => '💻',
                                    'Music' => '🎵', 'Food' => '🍕', 'Sports' => '🏀', 'Community' => '🤝',
                                ];
                            @endphp
                            {{ $vibeIcons[$event->vibe] ?? '' }}
                            {{ $event->vibe }}
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-none drop-shadow-lg">
                        {{ $event->title }}
                    </h1>
                </div>
            </div>

            {{-- Meta row --}}
            <div class="mt-6 flex flex-wrap items-center gap-4 text-white/80 text-sm sm:text-base">
                <div class="flex items-center gap-1.5">
                    <span>📅</span>
                    <span>{{ $event->event_date->format('l, F d, Y') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span>⏰</span>
                    <span>{{ $event->event_date->format('g:i A') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span>📍</span>
                    <span>{{ $event->location_name }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main content --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        @if($event->cover_image)
                            <img src="{{ $event->cover_image }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-64 sm:h-80 object-cover"
                                 onerror="this.parentElement.classList.add('hidden')" />
                        @endif

                        <div class="p-6 sm:p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">About this event</h2>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $event->description }}
                            </p>

                            {{-- Details --}}
                            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</p>
                                    <p class="mt-1 text-gray-900 font-medium">{{ $event->event_date->format('l, F d, Y') }}</p>
                                    <p class="text-gray-600 text-sm">{{ $event->event_date->format('g:i A') }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</p>
                                    <p class="mt-1 text-gray-900 font-medium">{{ $event->location_name }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Vibe</p>
                                    <p class="mt-1 text-gray-900 font-medium">{{ $event->vibe }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Organizer</p>
                                    <p class="mt-1 text-gray-900 font-medium">{{ $event->user->publicName() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-md p-6 sticky top-24">
                        <h3 class="font-bold text-gray-900 mb-4">Event Details</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">When</p>
                                <p class="mt-0.5 text-gray-800">{{ $event->event_date->format('M d, Y') }}</p>
                                <p class="text-gray-600 text-sm">{{ $event->event_date->format('g:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Where</p>
                                <p class="mt-0.5 text-gray-800">{{ $event->location_name }}</p>
                            </div>
                        </div>

                        <a href="{{ route('ganaps.index') }}"
                           class="mt-6 block w-full text-center px-4 py-3 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800 transition-colors">
                            Browse more ganaps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
