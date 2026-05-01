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
            RoleSeeder::class,
        ]);

        // Seed admin is now in RoleSeeder
        User::firstOrCreate(
            ['email' => 'user@iskina.ph'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'city_id' => 1,
                'reputation_tier' => 'regular',
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            ListingSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}
