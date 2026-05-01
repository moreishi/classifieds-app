<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'name', 'slug', 'region_id', 'parent_id', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(City::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(City::class, 'parent_id');
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
