<?php

use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\CameraController;
use App\Http\Controllers\Api\EntryExitController;
use App\Http\Controllers\Api\GarageController;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\LotController;
use App\Http\Controllers\Api\VehicleCountController;
use Illuminate\Support\Facades\Route;

// Parking lots and garages - GET endpoints
Route::get('/lots', [LotController::class, 'index']);
Route::get('/lots/{lot}', [LotController::class, 'show']);
Route::get('/garages', [GarageController::class, 'index']);
Route::get('/garages/{garage}', [GarageController::class, 'show']);

// Buildings and nearest parking
Route::get('/buildings', [BuildingController::class, 'index']);
Route::get('/buildings/{building}/nearest-parking', [BuildingController::class, 'nearestParking']);

// Data ingestion - POST endpoints
Route::post('/update_camera', [CameraController::class, 'update']);
Route::post('/update_gps', [GpsController::class, 'update']);
Route::post('/update_entry_exit', [EntryExitController::class, 'update']);
Route::post('/update_vehicle_count', [VehicleCountController::class, 'update']);

// Alerts - GET and POST endpoints
Route::get('/alerts', [AlertController::class, 'index']);
Route::get('/alerts/active', [AlertController::class, 'active']);
Route::post('/alerts', [AlertController::class, 'store']);
Route::put('/alerts/{alert}', [AlertController::class, 'update']);
Route::delete('/alerts/{alert}', [AlertController::class, 'destroy']);
