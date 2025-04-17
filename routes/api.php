<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\InspectionsController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);  // Correct for API
Route::get('/test-api', function () {
    return response()->json(['message' => 'API is reachable']);
});

// Inspections
Route::get('/inspections', [InspectionsController::class, 'apiIndex']);
Route::post('/inspections', [InspectionsController::class, 'store']);
