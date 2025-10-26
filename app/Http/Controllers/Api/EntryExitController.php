<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntryEvent;
use App\Models\Garage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryExitController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'garage_id' => 'required|exists:garages,id',
            'action' => 'required|in:entry,exit',
        ]);

        $result = DB::transaction(function () use ($validated) {
            EntryEvent::create([
                'garage_id' => $validated['garage_id'],
                'action' => $validated['action'],
                'event_at' => now(),
            ]);

            $garage = Garage::find($validated['garage_id']);

            if ($garage) {
                if ($validated['action'] === 'entry') {
                    if ($garage->available_spots > 0) {
                        $garage->decrement('available_spots');
                    }
                } else {
                    if ($garage->available_spots < $garage->total_spots) {
                        $garage->increment('available_spots');
                    }
                }

                return [
                    'garage_id' => $garage->id,
                    'action' => $validated['action'],
                    'available_spots' => $garage->fresh()->available_spots,
                    'total_spots' => $garage->total_spots,
                    'occupancy_percentage' => $garage->fresh()->occupancy_percentage,
                ];
            }

            return null;
        });

        return response()->json([
            'success' => true,
            'message' => 'Entry/exit event processed',
            'data' => $result,
        ]);
    }
}
