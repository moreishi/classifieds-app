<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCategoryPricesSeeder extends Seeder
{
    public function run(): void
    {
        // All non-Jobs categories → 100 centavos (₱1)
        $updated = DB::update(
            "UPDATE categories SET post_price = 100 WHERE id NOT IN (
                SELECT id FROM (
                    SELECT id FROM categories WHERE id = 5 OR parent_id = 5
                ) AS jobs_tree
            )"
        );

        // Ensure Jobs and subcategories stay free
        DB::update("UPDATE categories SET post_price = 0 WHERE id = 5 OR parent_id = 5");

        $this->command?->info("Updated {$updated} categories to 100 (₱1). Jobs remain free.");
    }
}
