<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\InspectionsController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\LocalReportController;
use App\Http\Controllers\FTLTController;
use App\Http\Controllers\BTSController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerminalController;

Route::post('/login', [LoginController::class, 'login']);  // Correct for API
Route::get('/test-api', function () {
    return response()->json(['message' => 'API is reachable']);
});

// Inspections
Route::get('/inspections', [InspectionsController::class, 'apiIndex']);
Route::post('/inspections', [InspectionsController::class, 'apiStore']);
Route::put('/inspections/{id}', [InspectionsController::class, 'apiUpdate']);
Route::delete('/inspections/{id}', [InspectionsController::class, 'apiDelete']);

//Complaints
Route::get('/complaints', [ComplaintsController::class, 'apiIndex']);
Route::post('/complaints', [ComplaintsController::class, 'apiStore']);
Route::put('/complaints/{id}', [ComplaintsController::class, 'apiUpdate']);
Route::delete('/complaints/{id}', [ComplaintsController::class, 'apiDelete']);
// New route to handle resolving a complaint from mobile
Route::post('/complaints/{id}/resolve', [ComplaintsController::class, 'apiResolve']);

//Local Report
Route::get('/local_report', [LocalReportController::class, 'apiIndex']);
Route::post('/local_report', [LocalReportController::class, 'apiStore']);
Route::put('/local_report/{id}', [LocalReportController::class, 'apiUpdate']);
Route::delete('/local_report/{id}', [LocalReportController::class, 'apiDelete']);

//FTLT
Route::get('/ftlt', [FTLTController::class, 'apiIndex']);
Route::post('/ftlt', [FTLTController::class, 'store']);
Route::post('/ftlt/check-in', [FTLTController::class, 'apiCheckIn']);
Route::post('/ftlt/check-out', [FTLTController::class, 'apiCheckOut']);
Route::put('/ftlt/{id}', [FTLTController::class, 'apiUpdate']);
Route::delete('/ftlt/{id}', [FTLTController::class, 'apiDelete']);

// BTS
Route::get('/bts', [BTSController::class, 'apiIndex']);
Route::post('/bts', [BTSController::class, 'apiStore']);
Route::put('/bts/{id}', [BTSController::class, 'apiUpdate']);
Route::delete('/bts/{id}', [BTSController::class, 'apiDelete']);

//Terminal id
Route::get('/search-terminal', [TerminalController::class, 'search']);
