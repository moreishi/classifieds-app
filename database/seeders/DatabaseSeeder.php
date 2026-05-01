<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RegionCitySeeder::class,
            CategorySeeder::class,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@iskina.ph',
            'password' => bcrypt('password'),
            'city_id' => 1,
            'reputation_tier' => 'pro',
        ]);

        $this->call([
            ListingSeeder::class,
        ]);
    }
}
