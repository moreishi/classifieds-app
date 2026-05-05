<div class="max-w-2xl mx-auto py-8 px-4 space-y-8">
    <h2 class="text-2xl font-bold text-gray-900">Settings</h2>

    {{-- Profile --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Profile</h3>
        <p class="text-sm text-gray-500">Your real name is used internally and never shown publicly.</p>

        @if(session()->has('profile_updated') || session()->has('avatar_updated'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md px-4 py-3 text-sm" role="alert">
                {{ session('profile_updated') ?? session('avatar_updated') }}
            </div>
        @endif

        {{-- Avatar --}}
        <div class="flex items-center gap-6">
            <div class="relative shrink-0">
                @if($showAvatarPreview && $newAvatar)
                    <img src="{{ $newAvatar->temporaryUrl() }}" alt="New avatar"
                         class="w-20 h-20 rounded-full object-cover border-2 border-blue-400">
                @else
                    <img src="{{ $user->avatar }}" alt="{{ $user->publicName() }}"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
                         class="w-20 h-20 rounded-full object-cover">
                    <div class="hidden w-20 h-20 rounded-full bg-blue-600 text-white items-center justify-center text-xl font-bold">
                        {{ $user->initials() }}
                    </div>
                @endif
            </div>
            <div class="flex flex-col gap-2">
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                    </svg>
                    Upload Photo
                    <input type="file" wire:model="newAvatar" accept="image/jpeg,image/png,image/webp" class="sr-only">
                </label>
                @error('newAvatar') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500">JPG, PNG or WebP. Max 2MB.</p>
            </div>
        </div>

        @if($showAvatarPreview)
            <div class="flex gap-2">
                <button type="button" wire:click="updateAvatar" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="updateAvatar">Save Photo</span>
                    <span wire:loading wire:target="updateAvatar">Saving...</span>
                </button>
                <button type="button" wire:click="$set('newAvatar', null); $set('showAvatarPreview', false)"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        @elseif($user->avatar_url && !str_starts_with($user->avatar_url, 'http'))
            {{-- Only show remove button for local uploads, not Google avatars --}}
            <div>
                <button type="button" wire:click="removeAvatar" wire:confirm="Remove your profile photo?"
                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 text-xs font-medium rounded-md hover:bg-red-50">
                    Remove Photo
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                <input id="firstName" type="text" wire:model="firstName" maxlength="100"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('firstName') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="middleName" class="block text-sm font-medium text-gray-700">Middle Name <span class="text-gray-400">(optional)</span></label>
                <input id="middleName" type="text" wire:model="middleName" maxlength="100"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('middleName') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input id="lastName" type="text" wire:model="lastName" maxlength="100"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('lastName') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="displayName" class="block text-sm font-medium text-gray-700">Display Name</label>
            <input id="displayName" type="text" wire:model="displayName" maxlength="100"
                   placeholder="How others see you — e.g. Juan dela Cruz"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('displayName') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 mt-1">This is shown across the site. Leave blank to use your username.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <div class="mt-1 flex items-center gap-2">
                <input id="username" type="text" wire:model="username" placeholder="johndoe" maxlength="50" disabled
                       class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed">
                <span class="text-xs text-gray-400 shrink-0">Cannot be changed</span>
            </div>
            @error('username') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 mt-1">Your public handle — other users see this instead of your real name.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1 flex items-center gap-2">
                <input id="email" type="email" wire:model="email" disabled
                       class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed">
                <span class="text-xs text-gray-400 shrink-0">Cannot be changed</span>
            </div>
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end">
            <button type="button" wire:click="updateProfile" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <span wire:loading.remove wire:target="updateProfile">Save Name</span>
                <span wire:loading wire:target="updateProfile">Saving...</span>
            </button>
        </div>
    </div>

    {{-- GCash & Credits --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">GCash &amp; Credits</h3>

        <div class="flex items-center gap-3">
            @if($user->gcash_verified_at)
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <div>
                    <div class="text-sm font-medium">Verified — {{ $user->gcash_number }}</div>
                    <div class="text-xs text-gray-500">Since {{ $user->gcash_verified_at->format('M j, Y') }}</div>
                </div>
            @elseif($user->gcash_number)
                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <div>
                    <div class="text-sm font-medium">Not verified — <a href="{{ route('verify-account') }}" class="underline text-blue-600 hover:text-blue-800">Verify now</a></div>
                </div>
            @else
                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <div>
                    <div class="text-sm font-medium">Not set</div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-yellow-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div>
                <div class="text-sm font-medium">
                    Balance: <strong>₱{{ number_format($user->credit_balance / 100, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('verify-account') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>
                Verify GCash
            </a>
            <a href="{{ route('buy-credits') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buy Credits
            </a>
        </div>

        @if($user->gcash_number)
            <hr class="border-gray-200 my-2">
            @if(session()->has('gcash_updated'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md px-4 py-3 text-sm" role="alert">
                    {{ session('gcash_updated') }}
                </div>
            @endif
            <div>
                <label for="gcashNumber" class="block text-sm font-medium text-gray-700">Change GCash Number</label>
                <input id="gcashNumber" type="text" wire:model="gcashNumber" placeholder="09171234567" maxlength="11"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('gcashNumber') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">Changing your number will require re-verification.</p>
            </div>
            <div class="flex justify-end">
                <button type="button" wire:click="updateGcash" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="updateGcash">Update Number</span>
                    <span wire:loading wire:target="updateGcash">Updating...</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Notification Preferences --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>

        @if(session()->has('notifications_updated'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md px-4 py-3 text-sm" role="alert">
                {{ session('notifications_updated') }}
            </div>
        @endif

        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" wire:model="notifyNewInquiry"
                   class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Email me when someone sends an inquiry about my listing</span>
        </label>

        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" wire:model="notifySellerReply"
                   class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Email me when a seller replies to my inquiry</span>
        </label>

        <div class="flex justify-end">
            <button type="button" wire:click="updateNotifications" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <span wire:loading.remove wire:target="updateNotifications">Save Preferences</span>
                <span wire:loading wire:target="updateNotifications">Saving...</span>
            </button>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4 border-l-4 border-red-400">
        <h3 class="text-lg font-semibold text-red-600">Danger Zone</h3>
        <p class="text-sm text-gray-600">Permanently delete your account and all associated data.</p>

        <button type="button" x-data @click="$dispatch('open-modal', 'delete-account')"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            Delete Account
        </button>
    </div>

    {{-- Delete Account Modal --}}
    <div x-data="{ open: false }"
         x-show="open"
         x-on:open-modal.window="if ($event.detail === 'delete-account') open = true"
         x-on:keydown.escape.window="open = false"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="open = false"></div>
            <div class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-auto">
                <h3 class="text-lg font-semibold text-gray-900">Delete Account?</h3>
                <p class="text-sm text-gray-600 mt-2">This action is permanent. Your listings, conversations, and credits will be lost.</p>

                @error('deletePassword') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                @error('deleteConfirm') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

                <div class="mt-4 space-y-3">
                    <div>
                        <label for="deletePassword" class="block text-sm font-medium text-gray-700">Type your password to confirm</label>
                        <input id="deletePassword" type="password" wire:model="deletePassword" placeholder="Password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="deleteConfirm" class="block text-sm font-medium text-gray-700">Type <strong>DELETE</strong> to confirm</label>
                        <input id="deleteConfirm" type="text" wire:model="deleteConfirm" placeholder="DELETE"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="button" @click="open = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" wire:click="deleteAccount" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50">
                        <span wire:loading.remove wire:target="deleteAccount">Delete My Account</span>
                        <span wire:loading wire:target="deleteAccount">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
