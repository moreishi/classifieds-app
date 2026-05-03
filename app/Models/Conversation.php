<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'listing_id',
        'buyer_id',
        'seller_id',
        'last_message_at',
        'buyer_archived_at',
        'seller_archived_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'buyer_archived_at' => 'datetime',
            'seller_archived_at' => 'datetime',
        ];
    }

    public function scopeNotArchivedBy(Builder $query, User $user): Builder
    {
        $column = $user->id === $this->buyer_id ? 'buyer_archived_at' : 'seller_archived_at';

        return $query->whereNull($column);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function otherUser(User $user): User
    {
        return $user->id === $this->buyer_id ? $this->seller : $this->buyer;
    }

    protected static function booted(): void
    {
        static::created(function (Conversation $conversation) {
            $conversation->update(['last_message_at' => now()]);
        });
    }
}
