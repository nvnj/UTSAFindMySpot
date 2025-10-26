<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lot>
 */
class LotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalSpots = fake()->numberBetween(50, 300);
        $availableSpots = fake()->numberBetween(0, $totalSpots);

        return [
            'name' => 'Lot '.fake()->bothify('??#'),
            'location' => fake()->streetAddress(),
            'latitude' => fake()->latitude(29.58, 29.59),
            'longitude' => fake()->longitude(-98.63, -98.61),
            'total_spots' => $totalSpots,
            'available_spots' => $availableSpots,
            'type' => fake()->randomElement(['open', 'covered', 'reserved']),
            'is_active' => true,
        ];
    }
}
