@props(['notifications', 'unreadCount' => 0, 'full' => false])

@forelse($notifications as $notification)
    @php $data = $notification->data; @endphp
    <div class="px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-blue-50/50' }}"
         wire:key="{{ $notification->id }}">
        <div class="flex items-start gap-3">
            {{-- Icon --}}
            <div class="shrink-0 mt-0.5">
                @switch($data['type'] ?? '')
                    @case('offer_received')
                    @case('offer_accepted')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 text-yellow-600 text-xs font-bold">₱</span>
                        @break
                    @case('transaction_completed')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100 text-green-600 text-xs font-bold">✓</span>
                        @break
                    @case('review_received')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-purple-100 text-purple-600 text-xs font-bold">★</span>
                        @break
                    @case('credits_low')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 text-red-600 text-xs font-bold">!</span>
                        @break
                    @default
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">●</span>
                @endswitch
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900 {{ $notification->read_at ? '' : 'font-semibold' }}">
                    @switch($data['type'] ?? '')
                        @case('offer_received')
                            Offer from <strong>{{ $data['buyer_name'] ?? 'someone' }}</strong>
                            — ₱{{ number_format(($data['amount'] ?? 0) / 100) }}
                            @break
                        @case('offer_accepted')
                            Your offer of ₱{{ number_format(($data['amount'] ?? 0) / 100) }} was accepted
                            @break
                        @case('transaction_completed')
                            Transaction completed for <strong>{{ $data['listing_title'] ?? '' }}</strong>
                            @break
                        @case('review_received')
                            <strong>{{ $data['reviewer_name'] ?? 'Someone' }}</strong> left a review
                            @break
                        @case('credits_low')
                            Credits running low — only <strong>₱{{ number_format(($data['current_balance'] ?? $data['balance'] ?? 0) / 100) }}</strong> left
                            @break
                        @default
                            {{ Str::headline($data['type'] ?? 'Notification') }}
                    @endswitch
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $notification->created_at->diffForHumans() }}
                    @if(!$notification->read_at)
                        <span class="text-blue-500 ml-1">· New</span>
                    @endif
                </p>
            </div>

            {{-- Mark read button --}}
            @if(!$notification->read_at)
                <button wire:click="markAsRead('{{ $notification->id }}')"
                        class="shrink-0 text-gray-300 hover:text-blue-500 transition-colors"
                        title="Mark as read">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="px-4 py-8 text-center text-gray-400 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        No notifications yet
    </div>
@endforelse
