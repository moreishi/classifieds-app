<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Top-level categories — insert or update if already exists
        $categories = [
            ['id' => 1, 'name' => 'Gadgets', 'slug' => 'gadgets', 'icon' => '📱', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 10],
            ['id' => 2, 'name' => 'Vehicles', 'slug' => 'vehicles', 'icon' => '🚗', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 3],
            ['id' => 3, 'name' => 'Property', 'slug' => 'property', 'icon' => '🏠', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 2],
            ['id' => 4, 'name' => 'General', 'slug' => 'general', 'icon' => '📦', 'post_price' => 100, 'free_listings_unverified' => 3, 'free_listings_verified' => 15],
            ['id' => 5, 'name' => 'Jobs', 'slug' => 'jobs', 'icon' => '💼', 'post_price' => 0, 'free_listings_unverified' => 10, 'free_listings_verified' => 999],
            ['id' => 6, 'name' => 'Services', 'slug' => 'services', 'icon' => '🔧', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['id' => 7, 'name' => 'Pets', 'slug' => 'pets', 'icon' => '🐾', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['id' => 8, 'name' => 'Agriculture', 'slug' => 'agriculture', 'icon' => '🌾', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['id' => $cat['id']], $cat);
        }

        // Subcategories — use updateOrInsert to be idempotent
        $subcategories = [
            ['parent_id' => 1, 'name' => 'Phones & Tablets', 'slug' => 'phones-tablets', 'icon' => '📱', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 10],
            ['parent_id' => 1, 'name' => 'Laptops & Computers', 'slug' => 'laptops-computers', 'icon' => '💻', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 10],
            ['parent_id' => 1, 'name' => 'Gaming & Consoles', 'slug' => 'gaming-consoles', 'icon' => '🎮', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 10],
            ['parent_id' => 1, 'name' => 'Audio & Wearables', 'slug' => 'audio-wearables', 'icon' => '🎧', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 10],
            ['parent_id' => 2, 'name' => 'Cars', 'slug' => 'cars', 'icon' => '🚗', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 3],
            ['parent_id' => 2, 'name' => 'Motorcycles & Scooters', 'slug' => 'motorcycles-scooters', 'icon' => '🏍️', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 3],
            ['parent_id' => 2, 'name' => 'Trucks & Vans', 'slug' => 'trucks-vans', 'icon' => '🚛', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 3],
            ['parent_id' => 3, 'name' => 'Apartments & Condos', 'slug' => 'apartments-condos', 'icon' => '🏢', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 2],
            ['parent_id' => 3, 'name' => 'Houses & Lots', 'slug' => 'houses-lots', 'icon' => '🏠', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 2],
            ['parent_id' => 3, 'name' => 'Rooms & Bedspace', 'slug' => 'rooms-bedspace', 'icon' => '🚪', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 2],
            ['parent_id' => 3, 'name' => 'Commercial Spaces', 'slug' => 'commercial-spaces', 'icon' => '🏪', 'post_price' => 100, 'free_listings_unverified' => 1, 'free_listings_verified' => 2],
            ['parent_id' => 4, 'name' => 'Home & Furniture', 'slug' => 'home-furniture', 'icon' => '🛋️', 'post_price' => 100, 'free_listings_unverified' => 3, 'free_listings_verified' => 15],
            ['parent_id' => 4, 'name' => 'Appliances', 'slug' => 'appliances', 'icon' => '🔌', 'post_price' => 100, 'free_listings_unverified' => 3, 'free_listings_verified' => 15],
            ['parent_id' => 4, 'name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'icon' => '⚽', 'post_price' => 100, 'free_listings_unverified' => 3, 'free_listings_verified' => 15],
            ['parent_id' => 4, 'name' => 'Fashion & Accessories', 'slug' => 'fashion-accessories', 'icon' => '👕', 'post_price' => 100, 'free_listings_unverified' => 3, 'free_listings_verified' => 15],
            ['parent_id' => 5, 'name' => 'IT & Tech Jobs', 'slug' => 'it-tech-jobs', 'icon' => '💻', 'post_price' => 0, 'free_listings_unverified' => 10, 'free_listings_verified' => 999],
            ['parent_id' => 5, 'name' => 'Service & Retail Jobs', 'slug' => 'service-retail-jobs', 'icon' => '🛍️', 'post_price' => 0, 'free_listings_unverified' => 10, 'free_listings_verified' => 999],
            ['parent_id' => 5, 'name' => 'Remote & Freelance', 'slug' => 'remote-freelance', 'icon' => '🌐', 'post_price' => 0, 'free_listings_unverified' => 10, 'free_listings_verified' => 999],
            ['parent_id' => 6, 'name' => 'Home & Repair Services', 'slug' => 'home-repair-services', 'icon' => '🔧', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['parent_id' => 6, 'name' => 'Health & Beauty', 'slug' => 'health-beauty', 'icon' => '💆', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['parent_id' => 6, 'name' => 'Events & Photography', 'slug' => 'events-photography', 'icon' => '📸', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['parent_id' => 6, 'name' => 'Transport & Logistics', 'slug' => 'transport-logistics', 'icon' => '🚚', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['parent_id' => 7, 'name' => 'Dogs', 'slug' => 'dogs', 'icon' => '🐕', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['parent_id' => 7, 'name' => 'Cats', 'slug' => 'cats', 'icon' => '🐱', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],
            ['parent_id' => 7, 'name' => 'Other Pets & Supplies', 'slug' => 'other-pets-supplies', 'icon' => '🐾', 'post_price' => 100, 'free_listings_unverified' => 2, 'free_listings_verified' => 5],

            // Agriculture subcategories
            ['parent_id' => 8, 'name' => 'Seeds & Seedlings', 'slug' => 'seeds-seedlings', 'icon' => '🌱', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Livestock & Poultry', 'slug' => 'livestock-poultry', 'icon' => '🐔', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Farming Tools & Equipment', 'slug' => 'farming-tools-equipment', 'icon' => '🔧', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Fertilizers & Pesticides', 'slug' => 'fertilizers-pesticides', 'icon' => '🧪', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Fresh Produce', 'slug' => 'fresh-produce', 'icon' => '🥬', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Organic & Sustainable', 'slug' => 'organic-sustainable', 'icon' => '🌿', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Fishery & Aquaculture', 'slug' => 'fishery-aquaculture', 'icon' => '🐟', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
            ['parent_id' => 8, 'name' => 'Other Agriculture', 'slug' => 'other-agriculture', 'icon' => '🧑‍🌾', 'post_price' => 0, 'free_listings_unverified' => 5, 'free_listings_verified' => 20],
        ];

        foreach ($subcategories as $sub) {
            DB::table('categories')->updateOrInsert(
                ['parent_id' => $sub['parent_id'], 'slug' => $sub['slug']],
                $sub
            );
        }
    }
}
