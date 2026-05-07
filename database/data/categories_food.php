<?php

return [
    // Top-level Food category - will get parent_id = null, id auto-assigned
    [
        'name' => 'Food & Beverages',
        'slug' => 'food-beverages',
        'icon' => '🍽️',
        'parent_slug' => null,
        'children' => [
            ['name' => 'Rice & Grains', 'slug' => 'rice-grains', 'icon' => '🍚'],
            ['name' => 'Meat & Poultry', 'slug' => 'meat-poultry', 'icon' => '🥩'],
            ['name' => 'Seafood', 'slug' => 'seafood', 'icon' => '🦐'],
            ['name' => 'Fruits & Vegetables', 'slug' => 'fruits-vegetables', 'icon' => '🥬'],
            ['name' => 'Dairy & Eggs', 'slug' => 'dairy-eggs', 'icon' => '🥛'],
            ['name' => 'Baked Goods & Pastry', 'slug' => 'baked-goods-pastry', 'icon' => '🥖'],
            ['name' => 'Beverages & Drinks', 'slug' => 'beverages-drinks', 'icon' => '🥤'],
            ['name' => 'Snacks & Packaged Food', 'slug' => 'snacks-packaged-food', 'icon' => '🍿'],
            ['name' => 'Condiments & Spices', 'slug' => 'condiments-spices', 'icon' => '🧂'],
            ['name' => 'Frozen & Processed', 'slug' => 'frozen-processed', 'icon' => '🧊'],
            ['name' => 'Catering Services', 'slug' => 'catering-services', 'icon' => '👨‍🍳'],
            ['name' => 'Home-Cooked & Ready-to-Eat', 'slug' => 'home-cooked-ready-to-eat', 'icon' => '🍱'],
            ['name' => 'Organic & Health Food', 'slug' => 'organic-health-food', 'icon' => '🥗'],
            ['name' => 'Food Supplements', 'slug' => 'food-supplements', 'icon' => '💊'],
            ['name' => 'Other Food & Beverages', 'slug' => 'other-food-beverages', 'icon' => '🛒'],
        ],
    ],
];
