<div>
    <div class="max-w-3xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Messages</h1>

        @if($conversations->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-lg">No conversations yet</p>
                <p class="text-gray-400 text-sm mt-1">Message a seller about a listing to get started.</p>
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
                    @endphp
                    <a href="{{ route('conversations.show', $conversation) }}"
                       class="block bg-white rounded-xl border hover:shadow-md transition-shadow p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-medium shrink-0">
                                {{ substr($otherUser->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900 truncate">
                                        {{ $otherUser->name }}
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
                            @if($unread > 0)
                                <span class="shrink-0 bg-blue-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                    {{ $unread }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-4">{{ $conversations->links() }}</div>
        @endif
    </div>
</div>
