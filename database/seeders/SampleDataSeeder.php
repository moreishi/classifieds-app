<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CreditTransaction;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    private const CONDITIONS = ['brand_new', 'like_new', 'used'];

    public function run(): void
    {
        $this->command->info('Seeding sample data...');

        // ── Cities (add 9 more to Cebu) ──
        $citiesJson = json_decode(file_get_contents(__DIR__ . '/data/cities.json'), true);
        $created = 0;
        foreach ($citiesJson as $city) {
            if (!City::where('slug', $city['slug'])->exists()) {
                City::create([
                    'id' => $city['id'],
                    'name' => $city['name'],
                    'slug' => $city['slug'],
                    'region_id' => 1,
                    'is_active' => true,
                ]);
                $created++;
            }
        }
        $this->command->info("Cities: {$created} added");

        // ── Users ──
        $usersJson = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);
        $created = 0;
        $cityIds = City::pluck('id')->toArray();
        foreach ($usersJson as $data) {
            if (User::where('email', $data['email'])->exists()) {
                continue;
            }

            $reputation = array_rand(array_flip(['newbie', 'regular', 'verified']));
            $points = match ($reputation) {
                'newbie' => rand(0, 50),
                'regular' => rand(60, 300),
                'verified' => rand(400, 800),
            };

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now()->subDays(rand(1, 90)),
                'city_id' => $cityIds[array_rand($cityIds)],
                'credit_balance' => rand(0, 20) * 50000,
                'reputation_points' => $points,
                'reputation_tier' => $reputation,
                'referral_code' => strtolower(Str::random(8)),
                'gcash_number' => '09' . str_pad(rand(10000000, 99999999), 9, '0', STR_PAD_LEFT),
                'gcash_verified_at' => rand(0, 1) ? now()->subDays(rand(1, 60)) : null,
            ]);
            $created++;
        }
        $this->command->info("Users: {$created} created");

        // ── Listings ──
        $listingsJson = json_decode(file_get_contents(__DIR__ . '/data/listings.json'), true);
        $userIds = User::where('email', '!=', 'admin@iskina.ph')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->error('No non-admin users found. Skipping listings.');
            return;
        }

        $created = 0;
        $statuses = ['active', 'active', 'active', 'sold']; // ~25% sold
        $seededListings = [];

        foreach ($listingsJson as $data) {
            $condition = $data['condition'] ?? self::CONDITIONS[array_rand(self::CONDITIONS)];
            $status = $statuses[array_rand($statuses)];
            $cityId = $cityIds[array_rand($cityIds)];
            $userId = $userIds[array_rand($userIds)];

            $desc = $this->generateDescription($data['title'], $condition);

            $listing = Listing::create([
                'user_id' => $userId,
                'category_id' => $data['category_id'],
                'city_id' => $cityId,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $desc,
                'price' => $data['price'],
                'condition' => $condition,
                'status' => $status,
                'sold_at' => $status === 'sold' ? now()->subDays(rand(1, 30)) : null,
                'expires_at' => now()->addDays(rand(15, 45)),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(1, 5)),
            ]);

            $seededListings[] = $listing;
            $created++;
        }

        $this->command->info("Listings: {$created} created");

        // ── Photos for listings (via picsum.photos) ──
        $photoCount = 0;
        if (!function_exists('imagecreatefromstring')) {
            $this->command->warn('GD extension not available — skipping photo seeding.');
        } else {
            $picsumIds = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
            foreach ($seededListings as $i => $listing) {
                if (rand(0, 2) === 0) { // 33% skip, 66% get photos
                    continue;
                }

                $numPhotos = rand(1, 3);
                for ($j = 0; $j < $numPhotos; $j++) {
                    try {
                        $picsumId = $picsumIds[$i % count($picsumIds)];
                        $url = "https://picsum.photos/id/" . ($picsumId + $j) . "/600/400";
                        $listing->addMediaFromUrl($url)
                            ->toMediaCollection('photos');
                        $photoCount++;
                    } catch (\Exception $e) {
                        // Skip if network unavailable or GD missing
                        break;
                    }
                }
            }
            $this->command->info("Photos: {$photoCount} added to " . min($photoCount, count($seededListings)) . " listings");
        }

        // ── Credit Transactions for users ──
        $users = User::where('credit_balance', '>', 0)->get();
        $txCount = 0;
        foreach ($users as $user) {
            if (rand(0, 1)) {
                // Use raw DB insert to avoid morphs constraints on sample data
                \Illuminate\Support\Facades\DB::table('credit_transactions')->insert([
                    'user_id' => $user->id,
                    'amount' => $user->credit_balance,
                    'type' => 'deposit',
                    'reference_type' => 'App\\Models\\User',
                    'reference_id' => $user->id,
                    'notes' => 'Sample deposit',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
                $txCount++;
            }
        }
        $this->command->info("Credit transactions: {$txCount} created");

        $this->command->info('Sample data seeding complete!');
    }

    private function generateDescription(string $title, string $condition): string
    {
        $descriptions = [
            "Selling my {$title}. Well maintained and in excellent condition.\n\nFeel free to message for more details. Free meetup within Cebu City area.\n\nCash on pickup or GCash accepted.",
            "Brand new {$title} straight from the box. Bought but never used.\n\nPrice is negotiable for serious buyers. No lowballs please.\n\nLocated in Cebu. Can ship nationwide.",
            "Used {$title} in great running condition. No issues whatsoever.\n\nOriginal receipt available. With freebies included.\n\nDM for inquiries or viewing schedule.",
            "Urgent sale: {$title}. Moving out so need to let this go.\n\nStill works perfectly. Just needs a new home.\n\nPickup in Mandaue or Lapu-Lapu.",
            "For sale: {$title}. Gently used for only a few months.\n\nComplete with box and accessories.\n\nPrice is firm. Text or DM only.",
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
