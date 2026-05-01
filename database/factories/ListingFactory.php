<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'city_id' => City::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->numberBetween(10000, 500000),
            'status' => 'active',
            'condition' => fake()->randomElement(['new', 'like_new', 'good', 'fair']),
            'expires_at' => now()->addDays(30),
        ];
    }
}
