<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Offer extends Model
{
    protected $fillable = [
        'listing_id', 'buyer_id', 'seller_id',
        'amount', 'message', 'status',
        'counter_amount', 'counter_message', 'countered_at',
    ];

    protected function casts(): array
    {
        return [
            'countered_at' => 'datetime',
        ];
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

    public function creditTransactions(): MorphMany
    {
        return $this->morphMany(CreditTransaction::class, 'reference');
    }
}
