<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryPricingOverride extends Model
{
    protected $fillable = [
        'category_id', 'city_id', 'post_price',
        'free_listings_unverified', 'free_listings_verified',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
