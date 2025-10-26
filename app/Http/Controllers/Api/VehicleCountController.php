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
            'vehicle_count' => 'required|integer|min:0',
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

        // Calculate available spots based on vehicle count
        $occupiedSpots = min($validated['vehicle_count'], $lot->total_spots);
        $availableSpots = max(0, $lot->total_spots - $occupiedSpots);

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
                'available_spots' => $lot->available_spots,
                'occupied_spots' => $occupiedSpots,
                'vehicle_count' => $validated['vehicle_count'],
            ],
        ]);
    }
}
