<div class="max-w-2xl mx-auto py-8 px-4 space-y-8">
    <flux:heading size="xl">Settings</flux:heading>

    {{-- Profile --}}
    <flux:card class="space-y-4">
        <flux:heading>Profile</flux:heading>

        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input wire:model="name" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input wire:model="email" type="email" />
            <flux:error name="email" />
        </flux:field>

        <div class="flex justify-end">
            <flux:button wire:click="updateProfile" variant="primary">Save</flux:button>
        </div>
    </flux:card>

    {{-- GCash & Credits --}}
    <flux:card class="space-y-4">
        <flux:heading>GCash &amp; Credits</flux:heading>

        <div class="flex items-center gap-3">
            <flux:icon.shield-check variant="solid" class="w-5 h-5 {{ $user->gcash_verified_at ? 'text-green-600' : 'text-gray-400' }}" />
            <div>
                <div class="text-sm font-medium">
                    @if($user->gcash_verified_at)
                        Verified — {{ $user->gcash_number }}
                    @elseif($user->gcash_number)
                        Not verified — <a href="{{ route('verify-account') }}" class="underline text-blue-600">Verify now</a>
                    @else
                        Not set
                    @endif
                </div>
                <div class="text-xs text-gray-500">
                    @if($user->gcash_verified_at)
                        Since {{ $user->gcash_verified_at->format('M j, Y') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:icon.coins variant="solid" class="w-5 h-5 text-yellow-600" />
            <div>
                <div class="text-sm font-medium">
                    Balance: <strong>₱{{ number_format($user->credit_balance / 100, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <flux:button href="{{ route('verify-account') }}" variant="primary" icon="credit-card">Verify GCash</flux:button>
            <flux:button href="{{ route('buy-credits') }}" icon="plus">Buy Credits</flux:button>
        </div>

        @if($user->gcash_number)
            <hr class="my-2">
            <flux:field>
                <flux:label>Change GCash Number</flux:label>
                <flux:input wire:model="gcashNumber" placeholder="09171234567" maxlength="11" />
                <flux:error name="gcashNumber" />
                <flux:description>Changing your number will require re-verification.</flux:description>
            </flux:field>
            <div class="flex justify-end">
                <flux:button wire:click="updateGcash" variant="primary">Update Number</flux:button>
            </div>
        @endif
    </flux:card>

    {{-- Notification Preferences --}}
    <flux:card class="space-y-4">
        <flux:heading>Notifications</flux:heading>

        <flux:field>
            <flux:checkbox wire:model="notifyNewInquiry" label="Email me when someone sends an inquiry about my listing" />
        </flux:field>

        <flux:field>
            <flux:checkbox wire:model="notifySellerReply" label="Email me when a seller replies to my inquiry" />
        </flux:field>

        <div class="flex justify-end">
            <flux:button wire:click="updateNotifications" variant="primary">Save Preferences</flux:button>
        </div>
    </flux:card>

    {{-- Danger Zone --}}
    <flux:card class="space-y-4 border-red-300">
        <flux:heading class="text-red-600">Danger Zone</flux:heading>
        <flux:text class="text-sm">Permanently delete your account and all associated data.</flux:text>

        <flux:modal.trigger name="delete-account">
            <flux:button variant="danger">Delete Account</flux:button>
        </flux:modal.trigger>
    </flux:card>

    {{-- Delete Account Modal --}}
    <flux:modal name="delete-account" class="md:w-96">
        <flux:heading>Delete Account?</flux:heading>
        <flux:text class="mt-2">This action is permanent. Your listings, conversations, and credits will be lost.</flux:text>

        <div class="mt-4 space-y-2">
            <flux:field>
                <flux:label>Type your password to confirm</flux:label>
                <flux:input type="password" wire:model="deletePassword" placeholder="Password" />
                <flux:error name="deletePassword" />
            </flux:field>
            <flux:field>
                <flux:label>Type <strong>DELETE</strong> to confirm</flux:label>
                <flux:input wire:model="deleteConfirm" placeholder="DELETE" />
                <flux:error name="deleteConfirm" />
            </flux:field>
        </div>

        <div class="flex gap-2 mt-6">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button variant="danger" wire:click="deleteAccount">Delete My Account</flux:button>
        </div>
    </flux:modal>
</div>
