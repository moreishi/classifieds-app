<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Gadgets', 'slug' => 'gadgets', 'icon' => '📱', 'post_price' => 5000, 'free_listings_unverified' => 2, 'free_listings_verified' => 10],
            ['name' => 'Vehicles', 'slug' => 'vehicles', 'icon' => '🚗', 'post_price' => 20000, 'free_listings_unverified' => 1, 'free_listings_verified' => 3],
            ['name' => 'Property', 'slug' => 'property', 'icon' => '🏠', 'post_price' => 50000, 'free_listings_unverified' => 1, 'free_listings_verified' => 2],
            ['name' => 'General', 'slug' => 'general', 'icon' => '📦', 'post_price' => 3000, 'free_listings_unverified' => 3, 'free_listings_verified' => 15],
            ['name' => 'Jobs', 'slug' => 'jobs', 'icon' => '💼', 'post_price' => 0, 'free_listings_unverified' => 10, 'free_listings_verified' => 999],
            ['name' => 'Services', 'slug' => 'services', 'icon' => '🔧', 'post_price' => 5000, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['name' => 'Pets', 'slug' => 'pets', 'icon' => '🐾', 'post_price' => 5000, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
        ]);
    }
}
