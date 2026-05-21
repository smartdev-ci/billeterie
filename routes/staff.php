<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\QRScanController;
use App\Http\Controllers\Staff\OrderManagementController;

Route::middleware(['auth', 'role:admin,organizer'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        
        Route::get('/', function () {
            return view('staff.dashboard');
        })->name('dashboard');

        // ✅ Correction : pointer vers 'validateTicket', PAS 'validate'
        Route::get('/qr/scan', [QRScanController::class, 'scan'])->name('qr.scan');
        Route::post('/qr/validate', [QRScanController::class, 'validateTicket'])->name('qr.validate');

        Route::get('/analytics', [\App\Http\Controllers\Staff\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export/csv', [\App\Http\Controllers\Staff\AnalyticsController::class, 'exportCsv'])->name('analytics.export.csv');
        Route::get('/analytics/export/pdf', [\App\Http\Controllers\Staff\AnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf');

        // Gestion des commandes
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [OrderManagementController::class, 'exportCsv'])->name('orders.export');
    });