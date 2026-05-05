<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Settings')]
class UserSettings extends Component
{
    use WithFileUploads;

    public User $user;

    // Profile — Name
    public string $firstName = '';
    public string $middleName = '';
    public string $lastName = '';
    public string $displayName = '';

    // Profile — Account
    public string $username = '';
    public string $email = '';

    // GCash
    public string $gcashNumber = '';

    // Notification preferences
    public bool $notifyNewInquiry = true;
    public bool $notifySellerReply = true;

    public string $deletePassword = '';
    public string $deleteConfirm = '';

    public $newAvatar = null;
    public bool $showAvatarPreview = false;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->firstName = $this->user->first_name ?? '';
        $this->middleName = $this->user->middle_name ?? '';
        $this->lastName = $this->user->last_name ?? '';
        $this->displayName = $this->user->display_name ?? '';
        $this->username = $this->user->username ?? '';
        $this->email = $this->user->email;
        $this->gcashNumber = $this->user->gcash_number ?? '';
        $this->notifyNewInquiry = $this->user->notify_new_inquiry ?? true;
        $this->notifySellerReply = $this->user->notify_seller_reply ?? true;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:100',
            'middleName' => 'nullable|string|max:100',
            'lastName' => 'required|string|max:100',
        ]);

        $this->user->update([
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName ?: null,
            'last_name' => $this->lastName,
            'display_name' => $this->displayName ?: null,
            'name' => trim(preg_replace('/\s+/', ' ', $this->firstName . ' ' . ($this->middleName ?? '') . ' ' . $this->lastName)),
        ]);

        session()->flash('profile_updated', 'Profile updated successfully.');
    }

    public function updatedNewAvatar(): void
    {
        $this->validate([
            'newAvatar' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $this->showAvatarPreview = true;
    }

    public function updateAvatar(): void
    {
        $this->validate([
            'newAvatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Delete old local avatar if exists
        if ($this->user->avatar_url && ! str_starts_with($this->user->avatar_url, 'http')) {
            Storage::disk('public')->delete($this->user->avatar_url);
        }

        $path = $this->newAvatar->store('avatars', 'public');

        $this->user->update([
            'avatar_url' => $path,
        ]);

        $this->newAvatar = null;
        $this->showAvatarPreview = false;

        session()->flash('avatar_updated', 'Profile photo updated successfully.');
    }

    public function removeAvatar(): void
    {
        if ($this->user->avatar_url && ! str_starts_with($this->user->avatar_url, 'http')) {
            Storage::disk('public')->delete($this->user->avatar_url);
        }

        $this->user->update([
            'avatar_url' => null,
        ]);

        $this->newAvatar = null;
        $this->showAvatarPreview = false;

        session()->flash('avatar_updated', 'Profile photo removed.');
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
            session()->flash('gcash_updated', 'GCash number updated. Please verify it again.');
        } elseif ($this->gcashNumber === '') {
            $this->user->update([
                'gcash_number' => null,
                'gcash_verified_at' => null,
            ]);
            session()->flash('gcash_updated', 'GCash number removed.');
        }
    }

    public function updateNotifications(): void
    {
        $this->user->update([
            'notify_new_inquiry' => $this->notifyNewInquiry,
            'notify_seller_reply' => $this->notifySellerReply,
        ]);

        session()->flash('notifications_updated', 'Notification preferences saved.');
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
