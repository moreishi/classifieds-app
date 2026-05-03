<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'first_name',
    'middle_name',
    'last_name',
    'username',
    'email',
    'password',
    'city_id',
    'gcash_number',
    'gcash_verified_at',
    'credit_balance',
    'reputation_points',
    'reputation_tier',
    'buyer_points',
    'free_listings_used',
    'free_listings_reset_at',
    'referral_code',
    'referred_by',
    'avatar_url',
    'last_active_at',
    'notify_new_inquiry',
    'notify_seller_reply',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \Spatie\Permission\Traits\HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'gcash_verified_at' => 'datetime',
            'free_listings_reset_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
            'notify_new_inquiry' => 'boolean',
            'notify_seller_reply' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function offersMade(): HasMany
    {
        return $this->hasMany(Offer::class, 'buyer_id');
    }

    public function receivedOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'seller_id');
    }

    public function conversationsAsBuyer(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function conversationsAsSeller(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function getAllConversationsAttribute()
    {
        return Conversation::where(function ($q) {
            $q->where('buyer_id', $this->id)
              ->orWhere('seller_id', $this->id);
        })->orderBy('last_message_at', 'desc');
    }

    public function favoriteListings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_user')
            ->withTimestamps()
            ->latest('listing_user.created_at');
    }

    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }

        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?s=80&d=mp";
    }

    public function publicName(): string
    {
        return $this->username ?? $this->fullName();
    }

    public function fullName(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return $parts ? implode(' ', $parts) : ($this->name ?? '');
    }

    public function initials(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);
        if (! $parts) {
            return strtoupper(substr($this->name ?? $this->username ?? '', 0, 2));
        }

        return collect($parts)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');
    }

    public function archivedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id')
            ->whereNotNull('buyer_archived_at')
            ->orWhere(function ($q) {
                $q->where('seller_id', $this->id)
                  ->whereNotNull('seller_archived_at');
            });
    }

    public function isGcashVerified(): bool
    {
        return ! is_null($this->gcash_verified_at);
    }
}
