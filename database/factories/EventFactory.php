<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(4),
            'description' => fake()->paragraphs(2, true),
            'event_date' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'location_name' => fake()->city() . ', Cebu',
            'vibe' => fake()->randomElement(['Party', 'Hustle', 'Art', 'Tech', 'Music', 'Food', 'Sports', 'Community']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withVibe(string $vibe): static
    {
        return $this->state(fn(array $attributes) => [
            'vibe' => $vibe,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn(array $attributes) => [
            'event_date' => fake()->dateTimeBetween('-2 months', '-1 day'),
        ]);
    }
}
