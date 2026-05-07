<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'icon', 'post_price',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function pricingOverrides(): HasMany
    {
        return $this->hasMany(CategoryPricingOverride::class);
    }

    public static function getActiveParents(): Collection
    {
        return Cache::remember('categories.parents', 86400, function () {
            return static::where('is_active', true)->whereNull('parent_id')->get();
        });
    }

    public static function getAllActive(): Collection
    {
        return Cache::remember('categories.all', 86400, function () {
            return static::where('is_active', true)->get();
        });
    }

    public static function findBySlugCached(string $slug): ?self
    {
        return Cache::remember("categories.slug.{$slug}", 86400, function () use ($slug) {
            return static::where('slug', $slug)->where('is_active', true)->first();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('categories.parents');
        Cache::forget('categories.all');
    }
}
