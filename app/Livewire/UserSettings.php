<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;

#[Title('Settings')]
class UserSettings extends Component
{
    public User $user;

    // Profile
    #[Rule('required|string|max:50|alpha_dash|unique:users,username')]
    public string $username = '';

    #[Rule('nullable|string|max:255')]
    public string $displayName = '';

    #[Rule('required|email|max:255')]
    public string $email = '';

    // GCash
    #[Rule('nullable|regex:/^09\d{9}$/')]
    public string $gcashNumber = '';

    // Notification preferences
    public bool $notifyNewInquiry = true;
    public bool $notifySellerReply = true;

    public string $deletePassword = '';
    public string $deleteConfirm = '';

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->username = $this->user->username ?? '';
        $this->displayName = $this->user->name;
        $this->email = $this->user->email;
        $this->gcashNumber = $this->user->gcash_number ?? '';
        $this->notifyNewInquiry = $this->user->notify_new_inquiry ?? true;
        $this->notifySellerReply = $this->user->notify_seller_reply ?? true;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $this->user->id,
            'displayName' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
        ]);

        $this->user->update([
            'username' => $this->username,
            'name' => $this->displayName ?: $this->username,
            'email' => $this->email,
        ]);

        $this->dispatch('notify', message: 'Profile updated.', variant: 'success');
    }

    public function updateGcash(): void
    {
        $this->validate([
            'gcashNumber' => 'nullable|regex:/^09\d{9}$/',
        ]);

        if ($this->gcashNumber && $this->gcashNumber !== $this->user->gcash_number) {
            $this->user->update([
                'gcash_number' => $this->gcashNumber,
                'gcash_verified_at' => null,
            ]);
            $this->dispatch('notify', message: 'GCash number updated. Please verify it again.', variant: 'warning');
        } elseif ($this->gcashNumber === '') {
            $this->user->update([
                'gcash_number' => null,
                'gcash_verified_at' => null,
            ]);
            $this->dispatch('notify', message: 'GCash number removed.', variant: 'success');
        }
    }

    public function updateNotifications(): void
    {
        $this->user->update([
            'notify_new_inquiry' => $this->notifyNewInquiry,
            'notify_seller_reply' => $this->notifySellerReply,
        ]);

        $this->dispatch('notify', message: 'Notification preferences saved.', variant: 'success');
    }

    public function deleteAccount(): void
    {
        $this->validate([
            'deletePassword' => 'required|string',
            'deleteConfirm' => 'required|string|in:DELETE',
        ]);

        if (!Hash::check($this->deletePassword, $this->user->password)) {
            throw ValidationException::withMessages([
                'deletePassword' => 'The password you entered is incorrect.',
            ]);
        }

        $this->user->delete();

        Auth::logout();

        $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.user-settings');
    }
}
