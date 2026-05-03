<div>
    <div class="max-w-3xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
            <button wire:click="toggleShowArchived" class="text-sm text-gray-500 hover:text-gray-700 underline">
                {{ $showArchived ? '← Active Conversations' : 'Archived (' . auth()->user()->archivedConversations()->count() . ')' }}
            </button>
        </div>

        @if($conversations->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                @if($showArchived)
                    <p class="text-gray-500 text-lg">No archived conversations</p>
                    <button wire:click="toggleShowArchived" class="text-sm text-blue-600 hover:text-blue-800 underline mt-2">View active conversations</button>
                @else
                    <p class="text-gray-500 text-lg">No conversations yet</p>
                    <p class="text-gray-400 text-sm mt-1">Message a seller about a listing to get started.</p>
                @endif
            </div>
        @else
            <div class="space-y-2">
                @foreach($conversations as $conversation)
                    @php
                        $otherUser = $conversation->otherUser(auth()->user());
                        $lastMessage = $conversation->messages()->latest()->first();
                        $unread = $conversation->messages()
                            ->where('sender_id', '!=', auth()->id())
                            ->whereNull('read_at')
                            ->count();
                        $isArchived = $conversation->buyer_id === auth()->id()
                            ? $conversation->buyer_archived_at
                            : $conversation->seller_archived_at;
                    @endphp
                    <div class="flex items-center gap-3">
                        <a href="{{ route('conversations.show', $conversation) }}"
                           class="flex-1 block bg-white rounded-xl border hover:shadow-md transition-shadow p-4 @if($isArchived) opacity-60 @endif">
                            <div class="flex items-center gap-4">
                                <img src="{{ $otherUser->avatar }}" alt="" class="w-10 h-10 rounded-full shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-gray-900 truncate">
                                            {{ $otherUser->publicName() }}
                                        </h3>
                                        <span class="text-xs text-gray-400 shrink-0">
                                            {{ $conversation->last_message_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        Re: {{ $conversation->listing->title }}
                                    </p>
                                    @if($lastMessage)
                                        <p class="text-sm text-gray-600 truncate mt-0.5">
                                            {{ Str::limit($lastMessage->body, 100) }}
                                        </p>
                                    @endif
                                </div>
                                @if($unread > 0 && !$isArchived)
                                    <span class="shrink-0 bg-blue-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </div>
                        </a>
                        @if($isArchived)
                            <button wire:click="unarchive({{ $conversation->id }})"
                                    class="shrink-0 p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                                    title="Restore conversation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M9 11V7m6 4V7M4 7l1 13a2 2 0 002 2h10a2 2 0 002-2l1-13M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2" />
                                </svg>
                            </button>
                        @else
                            <button wire:click="archive({{ $conversation->id }})"
                                    class="shrink-0 p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                                    title="Archive conversation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $conversations->links() }}</div>
        @endif
    </div>
</div>
