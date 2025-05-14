<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ---------------------------------------------------------------------
// Controllers
// ---------------------------------------------------------------------
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ParkingRecordController;
use App\Http\Controllers\TerminalParkingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InspectionsController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\WfhRequestController; // Newly added WFH controller
use App\Http\Controllers\SupportController;
use App\Http\Controllers\LocalReportController; // Newly added Local Report controller
use App\Http\Controllers\FTLTController; // Newly added FTLT controller
use App\Http\Controllers\BTSController; // Newly added BTS controller
use App\Http\Controllers\TerminalController; // Newly added Terminal controller
use App\Http\Controllers\LocationController; // Newly added Location controller 5 May 2025
use App\Http\Controllers\CallInboundController; // Newly added Call Inbound controller on 5th May 2025 CC

// ---------------------------------------------------------------------
// Models
// ---------------------------------------------------------------------
use App\Models\SummaryReport;

// ---------------------------------------------------------------------
// Authentication & Breeze Routes
// ---------------------------------------------------------------------
require __DIR__ . '/auth.php';

// ---------------------------------------------------------------------
// Basic Routes (Home & Dashboard)
// ---------------------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard'); // Your custom dashboard
})->middleware(['auth'])->name('dashboard');

// ---------------------------------------------------------------------
// Profile & User Settings
// ---------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------------------------------------------------------------------
// Admin-Only Routes (Role: Admin)
// ---------------------------------------------------------------------
Route::middleware(['auth', 'checkRole:Admin'])->group(function () {
    // Role Management
    Route::get('/admin/settings', [RoleManagementController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings/assign', [RoleManagementController::class, 'assignRoles'])->name('admin.settings.assign');
    Route::get('/admin/support', [SupportController::class, 'adminIndex'])->name('admin.support.index');
    Route::put('/admin/support/{id}/mark-read', [SupportController::class, 'markRead'])->name('admin.support.markRead');
    Route::delete('/admin/support/{id}', [SupportController::class, 'destroy'])->name('admin.support.destroy');

    // User Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

// ---------------------------------------------------------------------
// Support Routes
// ---------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/support/form', [SupportController::class, 'form'])->name('support.form');
    Route::post('/support/submit', [SupportController::class, 'submit'])->name('support.submit');
});
// ---------------------------------------------------------------------
// Department Routes (Protected by Auth and Department Roles)
// ---------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    // HR Department (Admin or HR)
    Route::get('/departments/hr', [DepartmentController::class, 'hr'])
        ->middleware(['checkRole:Admin|HR'])
        ->name('departments.hr')
        ->missing(function () {
            return response()->view('errors.unauthorized', [], 403);
        });

    // Accounting (Admin or Accounting)
    Route::get('/departments/accounting', [DepartmentController::class, 'accounting'])
        ->middleware(['checkRole:Admin|Accounting'])
        ->name('departments.accounting')
        ->missing(function () {
            return response()->view('errors.unauthorized', [], 403);
        });

    // Operations (Admin or Operations)
    Route::get('/departments/operations', [DepartmentController::class, 'operations'])
        ->middleware(['checkRole:Admin|Operations'])
        ->name('departments.operations')
        ->missing(function () {
            return response()->view('errors.unauthorized', [], 403);
        });

    // Control Center (Admin or ControlCenter)
    Route::get('/departments/control-center', [DepartmentController::class, 'controlCenter'])
        ->middleware(['checkRole:Admin|ControlCenter'])
        ->name('departments.controlCenter')
        ->missing(function () {
            return response()->view('errors.unauthorized', [], 403);
        });

    // Technical (Admin or Technical)
    Route::get('/departments/technical', [DepartmentController::class, 'technical'])
        ->middleware(['checkRole:Admin|Technical|TechnicalLead'])
        ->name('departments.technical')
        ->missing(function () {
            return response()->view('errors.unauthorized', [], 403);
        });

    // Secretary (Admin or Secretary)
    Route::get('/departments/secretary', [DepartmentController::class, 'secretary'])
        ->middleware(['checkRole:Admin|Secretary'])
        ->name('departments.secretary')
        ->missing(function () {
            return response()->view('errors.unauthorized', [], 403);
        });
});

// ---------------------------------------------------------------------
// HR Department Routes (Prefixed)
// ---------------------------------------------------------------------
Route::prefix('hr')->group(function () {
    // Employee Routes: Accessible to all authenticated users for submitting/viewing their own WFH requests.
    Route::middleware('auth')->group(function () {
        Route::get('/wfh', [WfhRequestController::class, 'index'])->name('wfh.index');
        Route::post('/wfh', [WfhRequestController::class, 'store'])->name('wfh.store');
    });

    // Admin Routes: Accessible only to Admins for managing WFH requests.
    Route::middleware(['auth', 'checkRole:Admin'])->group(function () {
        // New route for the admin dashboard UI for WFH requests
        Route::get('/wfh/admin', [WfhRequestController::class, 'adminIndex'])->name('wfh.admin');
        Route::post('/wfh/{wfhRequest}/approve', [WfhRequestController::class, 'approve'])->name('wfh.approve');
        Route::post('/wfh/{wfhRequest}/reject', [WfhRequestController::class, 'reject'])->name('wfh.reject');
    });

    // Placeholder for Annual Leave page
    Route::get('/al', function () {
        return view('departments.hr.al.al-comingsoon'); // File: resources/views/departments/hr/al/al-comingsoon.blade.php
    })->name('hr.al');

    // Placeholder for Attendance page
    Route::get('/attendance', function () {
        return view('departments.hr.attendance.attendance-comingsoon'); // File: resources/views/departments/hr/attendance/attendance-comingsoon.blade.php
    })->name('hr.attendance');
});
// ---------------------------------------------------------------------
// Products, Parking, and Misc. Routes
// ---------------------------------------------------------------------
Route::get('/products', [ProductController::class, 'index']);
Route::get('/parking-records', [ParkingRecordController::class, 'index']);

// ---------------------------------------------------------------------
// Technical Department (Prefixed) Routes
// ---------------------------------------------------------------------
Route::prefix('technical')->group(function () {
    Route::get('/dashboard', function () {
        return view('departments.technical');
    })->name('technical.dashboard');

    Route::get('/report', [ReportController::class, 'index'])->name('technical-report');
    Route::get('/inspections', [InspectionsController::class, 'index'])->name('technical-inspections');
    Route::get('/complaint', [ComplaintsController::class, 'index'])->name('technical-complaints');
    Route::get('/terminal-parking', [TerminalParkingController::class, 'index'])->name('technical.terminal_parking');
    Route::get('/audit', [AuditController::class, 'index'])->name('technical.audit');

    // Additional feature route
    Route::get('/something-else', function () {
        return view('departments.technical.something.index');
    })->name('technical.something_else');

    // Local Report Routes
    Route::get('/local_report', [LocalReportController::class, 'index'])->name('technical-local_report');
    Route::get('/local_report/create', [LocalReportController::class, 'create'])->name('technical-local_report.create');
    Route::post('/local_report', [LocalReportController::class, 'store'])->name('technical-local_report.store');

    // FTLT Routes
    Route::get('/technical/ftlt', [FTLTController::class, 'index'])->name('ftlt.index');
    Route::get('/technical/ftlt/create', [FTLTController::class, 'create'])->name('ftlt.create');
    Route::post('/technical/ftlt', [FTLTController::class, 'store'])->name('ftlt.store');
    Route::get('/technical/ftlt/{id}/checkout', [FTLTController::class, 'checkoutForm'])->name('ftlt.checkout');
    Route::post('/technical/ftlt/{id}/checkout', [FTLTController::class, 'checkoutSubmit'])->name('ftlt.checkout.submit');

    // BTS Routes
    Route::get('/technical/bts', [BTSController::class, 'index'])->name('bts.index');
    Route::get('/technical/bts/create', [BTSController::class, 'create'])->name('bts.create');
    Route::post('/technical/bts', [BTSController::class, 'store'])->name('bts.store');
    Route::get('/technical/bts/{id}/attend', [BTSController::class, 'attend'])->name('bts.attend');
    Route::put('/technical/bts/{id}/update-attend', [BTSController::class, 'updateAttend'])->name('bts.updateAttend');
    Route::get('/technical/bts/search-terminals', [BTSController::class, 'searchTerminals'])->name('bts.searchTerminals');
    Route::put('/technical/bts/{id}/verify', [BTSController::class, 'verify'])->name('bts.verify');
    Route::put('/technical/bts/{id}/reassign', [BTSController::class, 'reassign'])->name('bts.reassign');

    //Complaint Routes for TECHNICAL
    Route::put('/complaints/{id}/mark-fixed', [ComplaintsController::class, 'markFixed'])->name('complaints.markFixed');
    Route::get('/complaints/{id}/mark-fixed', [ComplaintsController::class, 'markAsFixed'])->name('complaints.markFixed');
});

// ---------------------------------------------------------------------
// Control Center Department (Prefixed) Routes
// ---------------------------------------------------------------------
Route::prefix('controlcenter')->middleware(['auth', 'checkRole:Admin|ControlCenter'])->group(function () {
    Route::get('/dashboard', fn() => view('departments.control_center'))->name('departments.controlcenter');

    // Call Inbound Management (manual CRUD routes)
    Route::get('/call-inbound', [CallInboundController::class, 'index'])->name('controlcenter.callinbound.index');
    Route::get('/call-inbound/create', [CallInboundController::class, 'create'])->name('controlcenter.callinbound.create');
    Route::post('/call-inbound', [CallInboundController::class, 'store'])->name('controlcenter.callinbound.store');
    Route::get('/call-inbound/{id}/edit', [CallInboundController::class, 'edit'])->name('controlcenter.callinbound.edit');
    Route::put('/call-inbound/{id}', [CallInboundController::class, 'update'])->name('controlcenter.callinbound.update');
    Route::delete('/call-inbound/{id}', [CallInboundController::class, 'destroy'])->name('controlcenter.callinbound.destroy');
    Route::get('/call-inbound/export', [CallInboundController::class, 'export'])->name('controlcenter.callinbound.export');


    // Complaints
    Route::get('/complaint', [ComplaintsController::class, 'index'])->name('controlcenter-complaints');
    Route::get('/complaint/{id}/assign', [ComplaintsController::class, 'assign'])->name('complaints.assign');
    Route::put('/complaint/{id}/assign', [ComplaintsController::class, 'assignUpdate'])->name('complaints.assign.update');
    Route::get('/complaints/{id}/reassign', [ComplaintsController::class, 'reassign'])->name('complaints.reassign');
    Route::post('/complaints/{id}/reassign', [ComplaintsController::class, 'reassignUpdate'])->name('complaints.reassign.update');
    Route::put('/complaints/{id}/unassign', [ComplaintsController::class, 'unassign'])->name('complaints.unassign');
    // New for Attend flow
    Route::get('/complaints/{id}/attend', [ComplaintsController::class, 'attend'])->name('complaints.attend');
    Route::post('/complaints/{id}/attend', [ComplaintsController::class, 'submitAttendance'])->name('complaints.attend.submit');

    // BTS View (Shared)
    Route::get('/bts', [BTSController::class, 'controlCenterView'])->name('controlcenter.bts.index');
    Route::get('/bts/{id}/verify', [BTSController::class, 'verify'])->name('bts.controlcenter.verify');
});

// ---------------------------------------------------------------------
// Terminal Parking & Summary (Resources)
// ---------------------------------------------------------------------
Route::get('/technical/terminal-parking', [TerminalParkingController::class, 'index'])->name('technical.terminal_parking');
Route::get('/technical/terminal-parking/export-csv', [TerminalParkingController::class, 'exportCSV'])->name('technical.terminal_parking.export.csv');
Route::get('/technical/terminal-parking/export-excel', [TerminalParkingController::class, 'exportExcel'])->name('technical.terminal_parking.export.excel');

Route::resource('report', ReportController::class)->except(['show']);

// ---------------------------------------------------------------------
// Export Routes (CSV & Excel)
// ---------------------------------------------------------------------
Route::get('/report/export-csv', [ReportController::class, 'exportCSV'])->name('report.export.csv');
Route::get('/report/export-excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');

Route::get('/inspections/export/csv', [InspectionsController::class, 'exportCsv'])->name('inspections.export.csv');
Route::get('/inspections/export/excel', [InspectionsController::class, 'exportExcel'])->name('inspections.export.excel');

Route::get('/complaint/export/csv', [ComplaintsController::class, 'exportCsv'])->name('complaints.export.csv');
Route::get('/complaint/export/excel', [ComplaintsController::class, 'exportExcel'])->name('complaints.export.excel');

// ---------------------------------------------------------------------
// Logout Route
// ---------------------------------------------------------------------
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/register'); // or your desired post-logout destination
})->name('logout');

// ---------------------------------------------------------------------
// Resource Routes (Complaints, Inspections, etc.)
// ---------------------------------------------------------------------
Route::resource('complaints', ComplaintsController::class);
Route::resource('inspections', InspectionsController::class);

// Terminal for Fetching Branches based on Terminal ID IF WORKS
Route::get('/terminals-by-branch', [TerminalController::class, 'terminalsByBranch'])->name('terminals.byBranch');
// Terminal for Fetching Zones and Roads based on Branch ID
Route::get('/zones/{branch}', [LocationController::class, 'getZonesByBranch']);
Route::get('/roads/{zoneId}', [LocationController::class, 'getRoadsByZone']);
Route::get('/roads/{zone}', [LocationController::class, 'getRoadsByZone']);

//Terminal Search easy for users to find terminals specific
Route::get('/terminals/search', [TerminalController::class, 'search'])->name('terminals.search');

// API route for Terminal Search
Route::get('/api/terminals/search', [TerminalController::class, 'search'])->name('terminals.search');

Route::patch('/inspections/{id}/spotcheck', [InspectionsController::class, 'updateSpotcheck'])
    ->middleware('auth', 'checkRole:Admin')
    ->name('inspections.spotcheck');
