<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use Illuminate\Http\JsonResponse;

class GarageController extends Controller
{
    public function index(): JsonResponse
    {
        $permitType = request()->query('permit');

        $garages = Garage::query()
            ->with(['alerts' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->get()
            ->filter(function ($garage) use ($permitType) {
                return $garage->allowsPermit($permitType);
            })
            ->map(function ($garage) {
                return [
                    'id' => $garage->id,
                    'garage_code' => $garage->garage_code,
                    'name' => $garage->name,
                    'location' => $garage->location,
                    'latitude' => $garage->latitude,
                    'longitude' => $garage->longitude,
                    'levels' => $garage->levels,
                    'space_type' => $garage->space_type,
                    'total_spots' => $garage->total_spots,
                    'available_spots' => $garage->available_spots,
                    'occupancy_percentage' => $garage->occupancy_percentage,
                    'status' => $garage->available_spots > 0 ? 'available' : 'full',
                    'alerts' => $garage->alerts,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $garages,
        ]);
    }

    public function show(Garage $garage): JsonResponse
    {
        $garage->load([
            'spots',
            'alerts' => fn ($query) => $query->where('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $garage->id,
                'name' => $garage->name,
                'location' => $garage->location,
                'latitude' => $garage->latitude,
                'longitude' => $garage->longitude,
                'levels' => $garage->levels,
                'total_spots' => $garage->total_spots,
                'available_spots' => $garage->available_spots,
                'occupancy_percentage' => $garage->occupancy_percentage,
                'status' => $garage->available_spots > 0 ? 'available' : 'full',
                'spots' => $garage->spots,
                'alerts' => $garage->alerts,
            ],
        ]);
    }
}
