<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\InspectionsController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\LocalReportController;
use App\Http\Controllers\FTLTController;
use App\Http\Controllers\BTSController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\UserController; // Import the UserController fro 20th may 2025
use App\Http\Controllers\RoadController; //improt as of 21st may 2025
use App\Models\Complaint;
use Illuminate\Http\Request;

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
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/complaints', [ComplaintsController::class, 'apiIndex']);
});
Route::post('/complaints', [ComplaintsController::class, 'apiStore']);
Route::put('/complaints/{id}', [ComplaintsController::class, 'apiUpdate']);
Route::delete('/complaints/{id}', [ComplaintsController::class, 'apiDelete']);
// New route to handle resolving a complaint from mobile
Route::post('/complaints/{id}/resolve', [ComplaintsController::class, 'apiResolve']);
Route::get('/my-attended-complaints', [ComplaintsController::class, 'myAttendedComplaints']);
// New route to fetch complaints by terminal ID
Route::get('/complaints/latest-status-id', [ComplaintsController::class, 'latestStatusId']);

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
Route::post('/bts/{id}', [BTSController::class, 'apiUpdate']); // ✅ Laravel accepts via _method=PUT
Route::delete('/bts/{id}', [BTSController::class, 'apiDelete']);
Route::put('/bts/{id}', [BTSController::class, 'apiUpdate']);

//Terminal id
Route::get('/search-terminal', [TerminalController::class, 'search']);

//User Id for Mobile Login Profile
Route::middleware('auth:sanctum')->get('/me', [UserController::class, 'me']);

//Fetch Roads by Zone
Route::get('/roads/{zone}', [RoadController::class, 'getByZone']);
