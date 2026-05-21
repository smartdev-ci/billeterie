<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\QRScanController;

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

        Route::get('/analytics', [\App\Http\Controllers\Staff\AnalyticsController::class, 'index'])->name('analytics');
    });