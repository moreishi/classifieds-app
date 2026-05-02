<div>
    <div class="max-w-3xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('conversations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-2 block">&larr; All Messages</a>
                <h1 class="text-xl font-bold text-gray-900">{{ $conversation->listing->title }}</h1>
                <p class="text-sm text-gray-500">
                    Conversation with {{ $otherUser->name }}
                </p>
            </div>
            <a href="{{ route('listing.show', $conversation->listing->slug) }}"
               class="text-sm text-blue-600 hover:text-blue-800">View Listing &rarr;</a>
        </div>

        {{-- Messages --}}
        <div wire:poll.5s="refreshMessages"
             x-data="{ scrollToBottom() { $nextTick(() => { $el.scrollTop = $el.scrollHeight }) } }"
             x-init="scrollToBottom()"
             class="bg-white rounded-xl border p-4 space-y-2 mb-4 max-h-[60vh] overflow-y-auto">

            @if($messages->isEmpty())
                <p class="text-center text-gray-400 py-8">No messages yet. Send the first message!</p>
            @else
                @foreach($messages as $msg)
                    <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} gap-2 items-start">
                        @if($msg->sender_id !== auth()->id())
                            <img src="{{ $msg->sender->avatar }}" alt="" class="w-7 h-7 rounded-full mt-1 shrink-0" />
                        @endif
                        <div class="max-w-[75%] {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-2xl px-3 py-1.5">
                            <p class="text-sm whitespace-pre-wrap">{!! nl2br(e($msg->body)) !!}</p>
                            <p class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-400' }} mt-1 text-right">
                                {{ $msg->created_at->format('g:i A') }}
                                @if($msg->sender_id === auth()->id() && $msg->read_at)
                                    &middot; Read
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Input --}}
        <form wire:submit="sendMessage" class="flex gap-2">
            <input type="text" wire:model="newMessage" placeholder="Type your message..."
                   class="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm"
                   x-on:keydown.enter.prevent="$wire.sendMessage()" />
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition-colors shrink-0">
                Send
            </button>
        </form>
    </div>
</div>
