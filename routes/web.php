<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdempiereController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\Procurement\ProcurementController;
use App\Http\Controllers\AsetIT\AsetITController;
use App\Http\Controllers\EApproval\EApprovalController;

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

    // Adempiere ERP Status & Diagnostics
    Route::get('/adempiere/status',       [AdempiereController::class, 'status'])->name('adempiere.status');
    Route::post('/adempiere/clear-cache', [AdempiereController::class, 'clearCache'])->name('adempiere.clear-cache');

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
        Route::get('/surat-jalan',      [WarehouseController::class, 'suratJalan'])->name('surat-jalan');
        Route::post('/surat-jalan',     [WarehouseController::class, 'storeSuratJalan'])->name('surat-jalan.store');
        Route::get('/req-atk',          [WarehouseController::class, 'reqAtk'])->name('req-atk');
        Route::post('/req-atk',         [WarehouseController::class, 'storeReqAtk'])->name('req-atk.store');
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

        // Form Izin
        Route::get('/izin',               [HRController::class, 'formIzin'])->name('izin');
        Route::post('/izin',              [HRController::class, 'storeIzin'])->name('izin.store');

        // Pengajuan Dinas
        Route::get('/pengajuan-dinas',    [HRController::class, 'pengajuanDinas'])->name('pengajuan-dinas');
        Route::post('/pengajuan-dinas',   [HRController::class, 'storePengajuanDinas'])->name('pengajuan-dinas.store');

        // Pengajuan SPJ
        Route::get('/pengajuan-spj',      [HRController::class, 'pengajuanSpj'])->name('pengajuan-spj');
        Route::post('/pengajuan-spj',     [HRController::class, 'storePengajuanSpj'])->name('pengajuan-spj.store');

        // Laporan Tunjangan
        Route::get('/tunjangan',          [HRController::class, 'tunjanganReport'])->name('tunjangan');
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
        Route::get('/material-request',        [ProcurementController::class, 'materialRequest'])->name('material-request');
        Route::post('/material-request',       [ProcurementController::class, 'storeMR'])->name('material-request.store');
        Route::get('/purchase-order',          [ProcurementController::class, 'purchaseOrder'])->name('purchase-order');
        Route::post('/purchase-order',         [ProcurementController::class, 'storePO'])->name('purchase-order.store');
    });

    // Aset Inventaris IT Module
    Route::prefix('aset-it')->name('aset-it.')->group(function () {
        Route::get('/dashboard',         [AsetITController::class, 'dashboard'])->name('dashboard');
        Route::get('/assets',            [AsetITController::class, 'assets'])->name('assets');
        Route::get('/assets/create',     [AsetITController::class, 'create'])->name('assets.create');
        Route::post('/assets',           [AsetITController::class, 'store'])->name('assets.store');
    });

    // E-Approval Module
    Route::prefix('e-approval')->name('e-approval.')->group(function () {
        Route::get('/dashboard',              [EApprovalController::class, 'dashboard'])->name('dashboard');
        Route::get('/documents',              [EApprovalController::class, 'documents'])->name('documents');
        Route::get('/documents/create',       [EApprovalController::class, 'create'])->name('documents.create');
        Route::post('/documents',             [EApprovalController::class, 'store'])->name('documents.store');
        Route::post('/documents/{id}/approve',[EApprovalController::class, 'approve'])->name('documents.approve');
        Route::post('/documents/{id}/reject', [EApprovalController::class, 'reject'])->name('documents.reject');
    });

});
