<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('logo.png') }}" alt="Iskina.ph" class="h-8 w-auto sm:h-12">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('listings.create')" :active="request()->routeIs('listings.create')">
                        {{ __('Create Listing') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('offers.index')" :active="request()->routeIs('offers.*')">
                            {{ __('Offers') }}
                        </x-nav-link>
                        <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                            {{ __('Messages') }}
                            @php
                                $unreadTotal = \App\Models\Conversation::where(function ($q) {
                                    $q->where('buyer_id', auth()->id())
                                      ->orWhere('seller_id', auth()->id());
                                })->whereHas('messages', function ($q) {
                                    $q->where('sender_id', '!=', auth()->id())
                                      ->whereNull('read_at');
                                })->count();
                            @endphp
                            @if($unreadTotal > 0)
                                <span class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-blue-600 rounded-full">{{ $unreadTotal }}</span>
                            @endif
                        </x-nav-link>
                        <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                            {{ __('Transactions') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            @auth
                <!-- Notifications Bell -->
                <div class="hidden sm:flex sm:items-center">
                    @livewire('notifications')
                </div>

                <!-- Settings Dropdown (authenticated) -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <img src="{{ Auth::user()->avatar }}" alt="" class="w-6 h-6 rounded-full" />
                                <div>{{ Auth::user()->name }}</div>
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @php
                                $unreadMessages = \App\Models\Conversation::where(function ($q) {
                                    $q->where('buyer_id', auth()->id())
                                      ->orWhere('seller_id', auth()->id());
                                })->whereHas('messages', function ($q) {
                                    $q->where('sender_id', '!=', auth()->id())
                                      ->whereNull('read_at');
                                })->count();
                            @endphp

                            <x-dropdown-link :href="route('conversations.index')">
                                {{ __('Messages') }}
                                @if($unreadMessages > 0)
                                    <span class="ml-2 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">
                                        {{ $unreadMessages }}
                                    </span>
                                @endif
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('favorites.index')">
                                {{ __('My Favorites') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('Dashboard') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('seller.dashboard')">
                                {{ __('Analytics') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('listings.my')">
                                {{ __('My Listings') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('listings.trashed')">
                                {{ __('Trashed') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('settings')">
                                {{ __('Settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <!-- Login / Register (guest) -->
                <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Register</a>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('listings.create')" :active="request()->routeIs('listings.create')">
                    {{ __('Create Listing') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('offers.index')" :active="request()->routeIs('offers.*')">
                    {{ __('Offers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                    {{ __('Messages') }}
                    @php
                        $unreadTotalMobile = \App\Models\Conversation::where(function ($q) {
                            $q->where('buyer_id', auth()->id())
                              ->orWhere('seller_id', auth()->id());
                        })->whereHas('messages', function ($q) {
                            $q->where('sender_id', '!=', auth()->id())
                              ->whereNull('read_at');
                        })->count();
                    @endphp
                    @if($unreadTotalMobile > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">{{ $unreadTotalMobile }}</span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    {{ __('Transactions') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    {{ __('Notifications') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('favorites.index')">
                    {{ __('My Favorites') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('seller.dashboard')">
                    {{ __('Analytics') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('listings.my')">
                    {{ __('My Listings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('listings.trashed')">
                    {{ __('Trashed') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('settings')">
                    {{ __('Settings') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
