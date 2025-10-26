<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spot>
 */
class SpotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lot_id' => null,
            'garage_id' => null,
            'spot_number' => fake()->bothify('SPOT-###'),
            'level' => null,
            'occupied' => fake()->boolean(30),
            'last_updated_at' => now()->subMinutes(fake()->numberBetween(1, 120)),
        ];
    }
}
