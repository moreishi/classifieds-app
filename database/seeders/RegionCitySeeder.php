<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionCitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('regions')->insert([
            'id' => 1,
            'name' => 'Central Visayas',
        ]);

        DB::table('cities')->insert([
            'id' => 1,
            'name' => 'Cebu City',
            'slug' => 'cebu-city',
            'region_id' => 1,
            'is_active' => true,
        ]);
    }
}
