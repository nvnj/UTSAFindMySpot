<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GpsReport;
use App\Models\Lot;
use App\Models\Spot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'spot_id' => 'nullable|exists:spots,id',
            'parked' => 'required|boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $result = DB::transaction(function () use ($validated) {
            GpsReport::create([
                'lot_id' => $validated['lot_id'],
                'spot_id' => $validated['spot_id'] ?? null,
                'parked' => $validated['parked'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'reported_at' => now(),
            ]);

            if (isset($validated['spot_id'])) {
                $spot = Spot::find($validated['spot_id']);
                if ($spot) {
                    $wasOccupied = $spot->occupied;
                    $spot->update([
                        'occupied' => $validated['parked'],
                        'last_updated_at' => now(),
                    ]);

                    if ($wasOccupied !== $validated['parked']) {
                        $lot = Lot::find($validated['lot_id']);
                        if ($lot) {
                            if ($validated['parked']) {
                                $lot->decrement('available_spots');
                            } else {
                                $lot->increment('available_spots');
                            }
                        }
                    }

                    return [
                        'spot_updated' => true,
                        'spot_id' => $spot->id,
                        'occupied' => $validated['parked'],
                    ];
                }
            }

            $lot = Lot::find($validated['lot_id']);
            if ($lot && $validated['parked']) {
                $lot->decrement('available_spots');
            } elseif ($lot && ! $validated['parked']) {
                $lot->increment('available_spots');
            }

            return [
                'spot_updated' => false,
                'lot_updated' => true,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'GPS report processed',
            'data' => $result,
        ]);
    }
}
