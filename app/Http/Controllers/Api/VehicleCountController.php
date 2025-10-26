<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleCountController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lot_code' => 'required|string|exists:lots,lot_code',
            'vehicle_count' => 'nullable|integer|min:0',
            'occupied_spots' => 'nullable|array',
            'occupied_spots.*' => 'integer|min:1',
            'camera_id' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        $lot = Lot::where('lot_code', $validated['lot_code'])->first();

        if (!$lot) {
            return response()->json([
                'success' => false,
                'message' => 'Lot not found',
            ], 404);
        }

        // Get all spots for this lot
        $spots = $lot->spots()
            ->orderByRaw('CAST(SUBSTRING(spot_number, LOCATE("-", spot_number) + 1) AS UNSIGNED)')
            ->get();

        // If occupied_spots list is provided, use that for precise control
        if (isset($validated['occupied_spots'])) {
            $occupiedSlotIds = $validated['occupied_spots'];

            // Update each spot based on whether its ID is in the occupied list
            foreach ($spots as $spot) {
                // Extract spot number (e.g., "BK1-5" -> 5)
                $spotNumber = (int) substr($spot->spot_number, strpos($spot->spot_number, '-') + 1);
                $isOccupied = in_array($spotNumber, $occupiedSlotIds);

                $spot->update([
                    'occupied' => $isOccupied,
                    'last_updated_at' => now(),
                ]);
            }

            $occupiedCount = count($occupiedSlotIds);
            $availableSpots = max(0, $lot->total_spots - $occupiedCount);
        } else {
            // Fallback to vehicle_count method (mark first N spots as occupied)
            $occupiedCount = min($validated['vehicle_count'] ?? 0, $lot->total_spots);
            $availableSpots = max(0, $lot->total_spots - $occupiedCount);

            foreach ($spots as $index => $spot) {
                $shouldBeOccupied = $index < $occupiedCount;
                $spot->update([
                    'occupied' => $shouldBeOccupied,
                    'last_updated_at' => now(),
                ]);
            }
        }

        // Update lot available spots
        $lot->update([
            'available_spots' => $availableSpots,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle count updated successfully',
            'lot' => [
                'code' => $lot->lot_code,
                'name' => $lot->name,
                'total_spots' => $lot->total_spots,
                'available_spots' => $availableSpots,
                'occupied_spots' => $occupiedCount,
                'vehicle_count' => $occupiedCount,
            ],
        ]);
    }
}
