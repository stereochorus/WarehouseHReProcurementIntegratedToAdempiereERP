<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\Procurement\ProcurementController;

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth Routes
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('demo.auth')->group(function () {

    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Warehouse Module
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/dashboard',        [WarehouseController::class, 'dashboard'])->name('dashboard');
        Route::get('/inventory',        [WarehouseController::class, 'inventory'])->name('inventory');
        Route::get('/receiving',        [WarehouseController::class, 'receiving'])->name('receiving');
        Route::post('/receiving',       [WarehouseController::class, 'storeReceiving'])->name('receiving.store');
        Route::get('/issuing',          [WarehouseController::class, 'issuing'])->name('issuing');
        Route::post('/issuing',         [WarehouseController::class, 'storeIssuing'])->name('issuing.store');
        Route::get('/stock-movement',   [WarehouseController::class, 'stockMovement'])->name('stock-movement');
        Route::get('/reports',          [WarehouseController::class, 'reports'])->name('reports');
    });

    // HR Module
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/dashboard',        [HRController::class, 'dashboard'])->name('dashboard');
        Route::get('/employees',        [HRController::class, 'employees'])->name('employees');
        Route::get('/employees/create', [HRController::class, 'createEmployee'])->name('employees.create');
        Route::post('/employees',       [HRController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/attendance',       [HRController::class, 'attendance'])->name('attendance');
        Route::post('/attendance',      [HRController::class, 'storeAttendance'])->name('attendance.store');
        Route::get('/payroll',              [HRController::class, 'payroll'])->name('payroll');
        Route::get('/payroll/report',       [HRController::class, 'payrollReport'])->name('payroll.report');
        Route::get('/payroll/{emp_id}/slip',[HRController::class, 'slipGaji'])->name('payroll.slip');
        Route::get('/reports',              [HRController::class, 'reports'])->name('reports');

        // Pengajuan Cuti
        Route::get('/leaves',               [HRController::class, 'leaves'])->name('leaves');
        Route::get('/leaves/create',        [HRController::class, 'createLeave'])->name('leaves.create');
        Route::post('/leaves',              [HRController::class, 'storeLeave'])->name('leaves.store');
        Route::post('/leaves/{id}/approve', [HRController::class, 'approveLeave'])->name('leaves.approve');

        // Pengajuan Sakit
        Route::get('/sick-leaves',               [HRController::class, 'sickLeaves'])->name('sick-leaves');
        Route::get('/sick-leaves/create',        [HRController::class, 'createSickLeave'])->name('sick-leaves.create');
        Route::post('/sick-leaves',              [HRController::class, 'storeSickLeave'])->name('sick-leaves.store');
        Route::post('/sick-leaves/{id}/approve', [HRController::class, 'approveSickLeave'])->name('sick-leaves.approve');

        // Pengajuan Lembur
        Route::get('/overtime',               [HRController::class, 'overtime'])->name('overtime');
        Route::get('/overtime/create',        [HRController::class, 'createOvertime'])->name('overtime.create');
        Route::post('/overtime',              [HRController::class, 'storeOvertime'])->name('overtime.store');
        Route::post('/overtime/{id}/approve', [HRController::class, 'approveOvertime'])->name('overtime.approve');

        // Laporan Cuti, Sakit & Lembur
        Route::get('/leave-reports',        [HRController::class, 'leaveReports'])->name('leave-reports');
    });

    // eProcurement Module
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::get('/dashboard',        [ProcurementController::class, 'dashboard'])->name('dashboard');
        Route::get('/purchase-requests',       [ProcurementController::class, 'purchaseRequests'])->name('purchase-requests');
        Route::get('/purchase-requests/create',[ProcurementController::class, 'createPR'])->name('purchase-requests.create');
        Route::post('/purchase-requests',      [ProcurementController::class, 'storePR'])->name('purchase-requests.store');
        Route::get('/approvals',        [ProcurementController::class, 'approvals'])->name('approvals');
        Route::post('/approvals/{id}',  [ProcurementController::class, 'processApproval'])->name('approvals.process');
        Route::get('/reports',          [ProcurementController::class, 'reports'])->name('reports');
    });

});
