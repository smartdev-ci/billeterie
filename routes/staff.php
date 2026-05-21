<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\QRScanController;
use App\Http\Controllers\Staff\OrderManagementController;

Route::middleware(['auth', 'role:admin,organizer'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        
        Route::get('/', function () {
            return view('staff.dashboard.index');
        })->name('dashboard');

        Route::get('/dashboard/live', function () {
            $metrics = [
                'sold' => 0,
                'fill_rate' => 0,
                'revenue' => 0,
                'recent_validations' => 0
            ];
            return view('staff.dashboard.live', compact('metrics'));
        })->name('dashboard.live');

        Route::get('/dashboard/stats', function () {
            $metrics = [
                'sold' => rand(100, 500),
                'fill_rate' => rand(60, 95),
                'revenue' => rand(50000, 200000),
                'recent_validations' => rand(10, 50)
            ];
            return response()->json($metrics);
        })->name('dashboard.stats');

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