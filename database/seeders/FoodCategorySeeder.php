<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = require database_path('data/categories_food.php');

        foreach ($categories as $parent) {
            $parentSlug = $parent['slug'];

            // Upsert parent
            DB::table('categories')->updateOrInsert(
                ['slug' => $parentSlug],
                [
                    'name' => $parent['name'],
                    'slug' => $parentSlug,
                    'icon' => $parent['icon'] ?? null,
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );

            $parentId = DB::table('categories')
                ->where('slug', $parentSlug)
                ->value('id');

            foreach ($parent['children'] as $child) {
                DB::table('categories')->updateOrInsert(
                    ['slug' => $child['slug']],
                    [
                        'name' => $child['name'],
                        'slug' => $child['slug'],
                        'icon' => $child['icon'] ?? null,
                        'parent_id' => $parentId,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
