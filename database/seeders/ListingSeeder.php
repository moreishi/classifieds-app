<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Listing;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();

        Listing::create([
            'user_id' => $user->id,
            'category_id' => 1,
            'city_id' => 1,
            'title' => 'iPhone 14 Pro Max 256GB',
            'slug' => 'iphone-14-pro-max-256gb',
            'description' => 'Brand new, sealed box. Deep Purple.',
            'price' => 4500000,
            'condition' => 'brand_new',
            'expires_at' => now()->addDays(30),
        ]);
    }
}
