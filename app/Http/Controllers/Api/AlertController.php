<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(): JsonResponse
    {
        $alerts = Alert::query()
            ->with(['lot', 'garage'])
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $alerts,
        ]);
    }

    public function active(): JsonResponse
    {
        $alerts = Alert::query()
            ->with(['lot', 'garage'])
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>=', now());
            })
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $alerts,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'garage_id' => 'nullable|exists:garages,id',
            'alert_type' => 'required|in:closure,construction,event,maintenance,full',
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        $alert = Alert::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alert created successfully',
            'data' => $alert,
        ], 201);
    }

    public function update(Request $request, Alert $alert): JsonResponse
    {
        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'garage_id' => 'nullable|exists:garages,id',
            'alert_type' => 'sometimes|in:closure,construction,event,maintenance,full',
            'title' => 'sometimes|string|max:255',
            'details' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        $alert->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alert updated successfully',
            'data' => $alert->fresh(),
        ]);
    }

    public function destroy(Alert $alert): JsonResponse
    {
        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert deleted successfully',
        ]);
    }
}
