<?php

namespace Tests\Feature\Api;

use App\Models\Lot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_lots(): void
    {
        Lot::factory()->count(3)->create(['is_active' => true]);

        $response = $this->getJson('/api/lots');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'location',
                        'total_spots',
                        'available_spots',
                        'occupancy_percentage',
                        'status',
                    ],
                ],
            ])
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_specific_lot(): void
    {
        $lot = Lot::factory()->create([
            'name' => 'Test Lot',
            'total_spots' => 100,
            'available_spots' => 50,
        ]);

        $response = $this->getJson("/api/lots/{$lot->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $lot->id,
                    'name' => 'Test Lot',
                    'total_spots' => 100,
                    'available_spots' => 50,
                ],
            ]);
    }

    public function test_lot_calculates_occupancy_percentage_correctly(): void
    {
        $lot = Lot::factory()->create([
            'total_spots' => 100,
            'available_spots' => 30,
        ]);

        $response = $this->getJson("/api/lots/{$lot->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'occupancy_percentage' => 70.00,
                ],
            ]);
    }

    public function test_inactive_lots_are_not_included(): void
    {
        Lot::factory()->create(['is_active' => true]);
        Lot::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/lots');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
