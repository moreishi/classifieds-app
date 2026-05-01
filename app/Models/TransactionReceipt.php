<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionReceipt extends Model
{
    protected $fillable = [
        'listing_id', 'seller_id', 'buyer_email', 'buyer_name',
        'reference_number', 'amount', 'status', 'receipt_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'receipt_sent_at' => 'datetime',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
