<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Garage;
use App\Models\Lot;
use App\Services\ParkingPermitService;
use Illuminate\Http\JsonResponse;

class BuildingController extends Controller
{
    public function index(): JsonResponse
    {
        $buildings = Building::query()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function ($building) {
                return [
                    'id' => $building->id,
                    'code' => $building->code,
                    'name' => $building->name,
                    'category' => $building->category,
                    'latitude' => $building->latitude,
                    'longitude' => $building->longitude,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $buildings,
        ]);
    }

    public function nearestParking(Building $building): JsonResponse
    {
        $permitType = request()->query('permit');
        $permitService = app(ParkingPermitService::class);

        // Get all available lots and garages
        $lots = Lot::where('is_active', true)->get();
        $garages = Garage::where('is_active', true)->get();

        // Filter by permit
        $lots = $lots->filter(fn ($lot) => $lot->allowsPermit($permitType));
        $garages = $garages->filter(fn ($garage) => $garage->allowsPermit($permitType));

        // Calculate distances and sort
        $parkingOptions = [];

        foreach ($lots as $lot) {
            if ($lot->available_spots > 0) {
                $distance = $this->calculateDistance(
                    $building->latitude,
                    $building->longitude,
                    $lot->latitude,
                    $lot->longitude
                );

                $parkingOptions[] = [
                    'type' => 'lot',
                    'id' => $lot->id,
                    'code' => $lot->lot_code,
                    'name' => $lot->name,
                    'location' => $lot->location,
                    'latitude' => $lot->latitude,
                    'longitude' => $lot->longitude,
                    'available_spots' => $lot->available_spots,
                    'total_spots' => $lot->total_spots,
                    'occupancy_percentage' => $lot->occupancy_percentage,
                    'distance_meters' => round($distance),
                    'distance_feet' => round($distance * 3.28084),
                    'walk_time_minutes' => ceil($distance / 80), // Average walking speed 80m/min
                ];
            }
        }

        foreach ($garages as $garage) {
            if ($garage->available_spots > 0) {
                $distance = $this->calculateDistance(
                    $building->latitude,
                    $building->longitude,
                    $garage->latitude,
                    $garage->longitude
                );

                $parkingOptions[] = [
                    'type' => 'garage',
                    'id' => $garage->id,
                    'code' => $garage->garage_code,
                    'name' => $garage->name,
                    'location' => $garage->location,
                    'latitude' => $garage->latitude,
                    'longitude' => $garage->longitude,
                    'levels' => $garage->levels,
                    'available_spots' => $garage->available_spots,
                    'total_spots' => $garage->total_spots,
                    'occupancy_percentage' => $garage->occupancy_percentage,
                    'distance_meters' => round($distance),
                    'distance_feet' => round($distance * 3.28084),
                    'walk_time_minutes' => ceil($distance / 80),
                ];
            }
        }

        // Sort by distance
        usort($parkingOptions, fn ($a, $b) => $a['distance_meters'] <=> $b['distance_meters']);

        return response()->json([
            'success' => true,
            'building' => [
                'id' => $building->id,
                'code' => $building->code,
                'name' => $building->name,
                'category' => $building->category,
            ],
            'data' => array_slice($parkingOptions, 0, 5), // Top 5 nearest
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in meters.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
