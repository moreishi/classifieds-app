<?php

namespace Database\Factories;

use App\Models\LiveBeacon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiveBeaconFactory extends Factory
{
    protected $model = LiveBeacon::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->sentence(6),
            'latitude' => fake()->latitude(10, 12),
            'longitude' => fake()->longitude(123, 125),
            'location_name' => fake()->city(),
            'status' => 'live',
            'started_at' => now(),
        ];
    }

    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ended',
            'ended_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'live',
            'started_at' => now()->subHours(3),
        ]);
    }
}
