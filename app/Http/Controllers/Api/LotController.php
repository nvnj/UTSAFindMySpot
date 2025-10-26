<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use Illuminate\Http\JsonResponse;

class LotController extends Controller
{
    public function index(): JsonResponse
    {
        $permitType = request()->query('permit');

        $lots = Lot::query()
            ->with(['alerts' => fn ($query) => $query->where('is_active', true)])
            ->get() // Include both active and inactive lots to show closed ones
            ->filter(function ($lot) use ($permitType) {
                return $lot->allowsPermit($permitType);
            })
            ->map(function ($lot) {
                return [
                    'id' => $lot->id,
                    'lot_code' => $lot->lot_code,
                    'name' => $lot->name,
                    'location' => $lot->location,
                    'latitude' => $lot->latitude,
                    'longitude' => $lot->longitude,
                    'type' => $lot->type,
                    'space_type' => $lot->space_type,
                    'total_spots' => $lot->total_spots,
                    'available_spots' => $lot->available_spots,
                    'occupancy_percentage' => $lot->occupancy_percentage,
                    'status' => $lot->is_active ? ($lot->available_spots > 0 ? 'available' : 'full') : 'closed',
                    'is_active' => $lot->is_active,
                    'alerts' => $lot->alerts,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $lots,
        ]);
    }

    public function show(Lot $lot): JsonResponse
    {
        $lot->load([
            'spots',
            'alerts' => fn ($query) => $query->where('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lot->id,
                'name' => $lot->name,
                'location' => $lot->location,
                'latitude' => $lot->latitude,
                'longitude' => $lot->longitude,
                'type' => $lot->type,
                'total_spots' => $lot->total_spots,
                'available_spots' => $lot->available_spots,
                'occupancy_percentage' => $lot->occupancy_percentage,
                'status' => $lot->available_spots > 0 ? 'available' : 'full',
                'spots' => $lot->spots,
                'alerts' => $lot->alerts,
            ],
        ]);
    }
}
