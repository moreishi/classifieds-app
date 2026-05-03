<div>
    <div class="max-w-3xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('conversations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-2 block">&larr; All Messages</a>
                <h1 class="text-xl font-bold text-gray-900">{{ $conversation->listing->title }}</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span>Conversation with</span>
                    <img src="{{ $otherUser->avatar }}" alt="" class="w-5 h-5 rounded-full inline-block" />
                    <span class="font-medium text-gray-700">{{ $otherUser->name }}</span>
                    @php $minutesSince = $otherUser->last_active_at?->diffInMinutes(now()); @endphp
                    @if($minutesSince !== null && $minutesSince < 5)
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-green-600 text-xs">Online</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('listing.show', $conversation->listing->slug) }}"
               class="text-sm text-blue-600 hover:text-blue-800">View Listing &rarr;</a>
        </div>

        {{-- Messages --}}
        <div wire:poll.5s="refreshMessages"
             x-data="{
                 init() { this.scrollToBottom() },
                 scrollToBottom() { $nextTick(() => { $el.scrollTop = $el.scrollHeight }) }
             }"
             x-init="init()"
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
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-400' }}">
                                    {{ $msg->created_at->format('g:i A') }}
                                </span>
                                {{-- Read receipt --}}
                                @if($msg->sender_id === auth()->id())
                                    @if($msg->read_at)
                                        <svg class="w-3.5 h-3.5 text-blue-200" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M23.5 6.9l-1.5-1.5a.517.517 0 00-.4-.2c-.15 0-.3.07-.4.2L11.3 15.4l-4.5-4.5a.564.564 0 00-.9 0l-1.5 1.5c-.2.2-.2.5 0 .7l5.5 5.5c.1.1.25.2.4.2s.3-.07.4-.2L23.5 7.6c.2-.2.2-.5 0-.7zM15 9.5l-2.2-2.2c-.1-.1-.25-.2-.4-.2s-.3.07-.4.2l-5.5 5.5c-.2.2-.2.5 0 .7l1.5 1.5c.1.1.25.2.4.2s.3-.07.4-.2l5.5-5.5c.2-.2.2-.5 0-.7zm-7.5 9.2l-2.2-2.2c-.1-.1-.25-.2-.4-.2s-.3.07-.4.2l-4.5 4.5c-.2.2-.2.5 0 .7l1.5 1.5c.1.1.25.2.4.2s.3-.07.4-.2L7.5 19.4c.2-.2.2-.5 0-.7z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-blue-200" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M23.5 6.9l-1.5-1.5a.517.517 0 00-.4-.2c-.15 0-.3.07-.4.2L11.3 15.4l-4.5-4.5a.564.564 0 00-.9 0l-1.5 1.5c-.2.2-.2.5 0 .7l5.5 5.5c.1.1.25.2.4.2s.3-.07.4-.2L23.5 7.6c.2-.2.2-.5 0-.7z"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Typing indicator --}}
            @if($unreadCount > 0 && $messages->isNotEmpty() && $messages->last()->sender_id !== auth()->id())
                <div class="flex justify-start gap-2 items-start">
                    <img src="{{ $otherUser->avatar }}" alt="" class="w-7 h-7 rounded-full mt-1 shrink-0" />
                    <div class="bg-gray-100 text-gray-400 rounded-2xl px-4 py-2 text-sm">
                        <span class="flex gap-1">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input with typing events --}}
        <form wire:submit="sendMessage" class="flex gap-2">
            <input type="text"
                   wire:model="newMessage"
                   placeholder="Type your message..."
                   class="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm"
                   x-on:keydown.enter.prevent="$wire.sendMessage()" />
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition-colors shrink-0 disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage">
                Send
            </button>
        </form>
    </div>
</div>
