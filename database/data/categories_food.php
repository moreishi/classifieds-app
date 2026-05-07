<?php

return [
    // Top-level Food category - will get parent_id = null, id auto-assigned
    [
        'name' => 'Food & Beverages',
        'slug' => 'food-beverages',
        'parent_slug' => null,
        'children' => [
            ['name' => 'Rice & Grains', 'slug' => 'rice-grains'],
            ['name' => 'Meat & Poultry', 'slug' => 'meat-poultry'],
            ['name' => 'Seafood', 'slug' => 'seafood'],
            ['name' => 'Fruits & Vegetables', 'slug' => 'fruits-vegetables'],
            ['name' => 'Dairy & Eggs', 'slug' => 'dairy-eggs'],
            ['name' => 'Baked Goods & Pastry', 'slug' => 'baked-goods-pastry'],
            ['name' => 'Beverages & Drinks', 'slug' => 'beverages-drinks'],
            ['name' => 'Snacks & Packaged Food', 'slug' => 'snacks-packaged-food'],
            ['name' => 'Condiments & Spices', 'slug' => 'condiments-spices'],
            ['name' => 'Frozen & Processed', 'slug' => 'frozen-processed'],
            ['name' => 'Catering Services', 'slug' => 'catering-services'],
            ['name' => 'Home-Cooked & Ready-to-Eat', 'slug' => 'home-cooked-ready-to-eat'],
            ['name' => 'Organic & Health Food', 'slug' => 'organic-health-food'],
            ['name' => 'Food Supplements', 'slug' => 'food-supplements'],
            ['name' => 'Other Food & Beverages', 'slug' => 'other-food-beverages'],
        ],
    ],
];
