<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\InspectionsController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\LocalReportController;
use App\Http\Controllers\FTLTController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);  // Correct for API
Route::get('/test-api', function () {
    return response()->json(['message' => 'API is reachable']);
});

// Inspections
Route::get('/inspections', [InspectionsController::class, 'apiIndex']);
Route::post('/inspections', [InspectionsController::class, 'store']);

//Complaints
Route::get('/complaints', [ComplaintsController::class, 'index']);
Route::post('/complaints', [ComplaintsController::class, 'store']);

//Local Report
Route::get('/local_report', [LocalReportController::class, 'index']);
Route::post('/local_report', [LocalReportController::class, 'store']);

//FTLT
Route::get('/ftlt', [FTLTController::class, 'index']);
Route::post('/ftlt', [FTLTController::class, 'store']);
