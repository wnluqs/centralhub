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
use App\Http\Controllers\ParkingEntryController;
use App\Http\Controllers\OnSiteProjectController;
use App\Http\Controllers\TerminalParkingController;
use App\Http\Controllers\SummaryReportController;
use App\Http\Controllers\InspectionsController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ComplaintsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\WfhRequestController; // Newly added WFH controller
use App\Http\Controllers\SupportController;

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
Route::get('/api/parking-data', [ParkingEntryController::class, 'getParkingData']);

// ---------------------------------------------------------------------
// Technical Department (Prefixed) Routes
// ---------------------------------------------------------------------
Route::prefix('technical')->group(function () {
    Route::get('/dashboard', function () {
        return view('departments.technical');
    })->name('technical.dashboard');

    Route::get('/summary', [SummaryReportController::class, 'index'])->name('technical-summary');
    Route::get('/inspections', [InspectionsController::class, 'index'])->name('technical-inspections');
    Route::get('/complaint', [ComplaintsController::class, 'index'])->name('technical-complaints');
    Route::get('/terminal-parking', [TerminalParkingController::class, 'index'])->name('technical.terminal_parking');
    Route::get('/audit', [AuditController::class, 'index'])->name('technical.audit');

    // Additional feature route
    Route::get('/something-else', function () {
        return view('departments.technical.something.index');
    })->name('technical.something_else');
});

// ---------------------------------------------------------------------
// Control Center Department (Prefixed) Routes
// ---------------------------------------------------------------------
Route::prefix('controlcenter')->group(function () {
    Route::get('/dashboard', function () {
        return view('departments.control_center');
    })->name('departments.controlcenter');

    Route::get('/complaint', [ComplaintsController::class, 'index'])->name('controlcenter-complaints');
});

// ---------------------------------------------------------------------
// Terminal Parking & Summary (Resources)
// ---------------------------------------------------------------------
Route::get('/technical/terminal-parking', [TerminalParkingController::class, 'index'])->name('technical.terminal_parking');
Route::get('/technical/terminal-parking/export-csv', [TerminalParkingController::class, 'exportCSV'])->name('technical.terminal_parking.export.csv');
Route::get('/technical/terminal-parking/export-excel', [TerminalParkingController::class, 'exportExcel'])->name('technical.terminal_parking.export.excel');

Route::resource('summary', SummaryReportController::class)->except(['show']);

// ---------------------------------------------------------------------
// Export Routes (CSV & Excel)
// ---------------------------------------------------------------------
Route::get('/summary/export-csv', [SummaryReportController::class, 'exportCSV'])->name('summary.export.csv');
Route::get('/summary/export-excel', [SummaryReportController::class, 'exportExcel'])->name('summary.export.excel');

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
