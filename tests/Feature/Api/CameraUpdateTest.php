<?php

namespace Tests\Feature\Api;

use App\Models\Lot;
use App\Models\Spot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CameraUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_camera_reports(): void
    {
        $lot = Lot::factory()->create(['total_spots' => 10, 'available_spots' => 5]);
        $spot = Spot::factory()->create(['lot_id' => $lot->id, 'occupied' => false]);

        $response = $this->postJson('/api/update_camera', [
            'reports' => [
                [
                    'lot_id' => $lot->id,
                    'spot_id' => $spot->id,
                    'occupied' => true,
                    'camera_id' => 'CAM001',
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'updated_spots' => 1,
            ]);

        $this->assertDatabaseHas('spots', [
            'id' => $spot->id,
            'occupied' => true,
        ]);

        $this->assertDatabaseHas('camera_reports', [
            'lot_id' => $lot->id,
            'spot_id' => $spot->id,
            'occupied' => true,
            'camera_id' => 'CAM001',
        ]);
    }

    public function test_camera_update_decrements_available_spots_when_occupied(): void
    {
        $lot = Lot::factory()->create(['total_spots' => 10, 'available_spots' => 5]);
        $spot = Spot::factory()->create(['lot_id' => $lot->id, 'occupied' => false]);

        $this->postJson('/api/update_camera', [
            'reports' => [
                [
                    'lot_id' => $lot->id,
                    'spot_id' => $spot->id,
                    'occupied' => true,
                ],
            ],
        ]);

        $this->assertDatabaseHas('lots', [
            'id' => $lot->id,
            'available_spots' => 4,
        ]);
    }

    public function test_camera_update_increments_available_spots_when_freed(): void
    {
        $lot = Lot::factory()->create(['total_spots' => 10, 'available_spots' => 5]);
        $spot = Spot::factory()->create(['lot_id' => $lot->id, 'occupied' => true]);

        $this->postJson('/api/update_camera', [
            'reports' => [
                [
                    'lot_id' => $lot->id,
                    'spot_id' => $spot->id,
                    'occupied' => false,
                ],
            ],
        ]);

        $this->assertDatabaseHas('lots', [
            'id' => $lot->id,
            'available_spots' => 6,
        ]);
    }

    public function test_camera_update_requires_valid_lot(): void
    {
        $response = $this->postJson('/api/update_camera', [
            'reports' => [
                [
                    'lot_id' => 999,
                    'spot_id' => null,
                    'occupied' => true,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }
}
