{{-- Notifications Bell (dropdown) --}}
@if(!$showAll)
<div x-data="{ open: false }" class="relative"
     @notification-read.window="open = false"
     @click.outside="open = false">
    <button @click="open = ! open"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors focus:outline-none">
        {{-- Bell icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[1.25rem]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Mark all read
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            <x-notification-list :notifications="$notifications" :unreadCount="$unreadCount" />
        </div>

        <a href="{{ route('notifications.index') }}"
           class="block w-full text-center py-2.5 text-sm text-blue-600 font-medium border-t border-gray-100 hover:bg-gray-50 transition-colors">
            View all notifications
        </a>
    </div>
</div>

{{-- Full notifications page --}}
@else
<div class="max-w-3xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        @if($unreadCount > 0)
            <button wire:click="markAllAsRead"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Mark all as read
            </button>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden">
        <x-notification-list :notifications="$notifications" :unreadCount="$unreadCount" :full="true" />
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endif
