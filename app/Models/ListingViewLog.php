<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingViewLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'listing_id', 'ip_address', 'user_agent', 'user_id', 'viewed_at', 'is_unique',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'is_unique' => 'boolean',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
