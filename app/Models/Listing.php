<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Listing extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id', 'category_id', 'city_id',
        'title', 'slug', 'description', 'price',
        'condition', 'status', 'is_featured',
        'total_views', 'unique_views',
        'expires_at', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'expires_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function transactionReceipts(): HasMany
    {
        return $this->hasMany(TransactionReceipt::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function viewLogs(): HasMany
    {
        return $this->hasMany(ListingViewLog::class);
    }

    public function creditTransactions(): MorphMany
    {
        return $this->morphMany(CreditTransaction::class, 'reference');
    }
}
