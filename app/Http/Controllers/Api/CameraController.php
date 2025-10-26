<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CameraReport;
use App\Models\Lot;
use App\Models\Spot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CameraController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reports' => 'required|array',
            'reports.*.lot_id' => 'required|exists:lots,id',
            'reports.*.spot_id' => 'nullable|exists:spots,id',
            'reports.*.occupied' => 'required|boolean',
            'reports.*.camera_id' => 'nullable|string',
        ]);

        $updatedSpots = [];
        $errors = [];

        DB::transaction(function () use ($validated, &$updatedSpots, &$errors) {
            foreach ($validated['reports'] as $report) {
                try {
                    CameraReport::create([
                        'lot_id' => $report['lot_id'],
                        'spot_id' => $report['spot_id'] ?? null,
                        'occupied' => $report['occupied'],
                        'camera_id' => $report['camera_id'] ?? null,
                        'reported_at' => now(),
                    ]);

                    if (isset($report['spot_id'])) {
                        $spot = Spot::find($report['spot_id']);
                        if ($spot) {
                            $wasOccupied = $spot->occupied;
                            $spot->update([
                                'occupied' => $report['occupied'],
                                'last_updated_at' => now(),
                            ]);

                            if ($wasOccupied !== $report['occupied']) {
                                $lot = Lot::find($report['lot_id']);
                                if ($lot) {
                                    if ($report['occupied']) {
                                        $lot->decrement('available_spots');
                                    } else {
                                        $lot->increment('available_spots');
                                    }
                                }
                            }

                            $updatedSpots[] = [
                                'spot_id' => $spot->id,
                                'lot_id' => $report['lot_id'],
                                'occupied' => $report['occupied'],
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'report' => $report,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        });

        return response()->json([
            'success' => count($errors) === 0,
            'message' => 'Camera reports processed',
            'updated_spots' => count($updatedSpots),
            'spots' => $updatedSpots,
            'errors' => $errors,
        ]);
    }
}
