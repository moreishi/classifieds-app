<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'post_price',
        'free_listings_unverified', 'free_listings_verified',
        'fields', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function pricingOverrides(): HasMany
    {
        return $this->hasMany(CategoryPricingOverride::class);
    }
}
