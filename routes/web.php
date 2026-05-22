<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order\CheckoutController;
use App\Http\Controllers\Payment\MobileMoneyPaymentController;
use App\Http\Controllers\Staff\QRScanController;
use App\Http\Controllers\Staff\AnalyticsController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventConfigController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Staff\OrderManagementController;
use App\Http\Controllers\Staff\LiveDashboardController;
use App\Http\Controllers\TicketController;

use App\Models\Order;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Guest + Auth User)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.home', [
        'event' => \App\Models\Event::first(),
        'events' => [],
        'testimonials' => collect(),
        'faqItems' => collect(),
    ]);
})->name('home');
Route::view('/event', 'public.event')->name('event.show');

// Checkout (Guest ou connecté)
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout.store')
    ->middleware('throttle:5,1');

// Simulation de paiement (dev only)
Route::post('/checkout/simulate/success', [CheckoutController::class, 'simulateSuccess'])->name('checkout.simulate.success');
Route::post('/checkout/simulate/failure', [CheckoutController::class, 'simulateFailure'])->name('checkout.simulate.failure');
Route::post('/checkout/simulate/pending', [CheckoutController::class, 'simulatePending'])->name('checkout.simulate.pending');

// Payment simulation & callback
Route::get('/payment/simulate/{order}', [MobileMoneyPaymentController::class, 'showSimulation'])
    ->name('payment.simulate');
Route::post('/payments/mobile-money/callback', [MobileMoneyPaymentController::class, 'callback'])
    ->name('payment.callback');
// Webhook pour les vrais paiements (OM/MTN)
Route::post('/payments/webhook', [MobileMoneyPaymentController::class, 'webhook'])
    ->name('payment.webhook');
Route::get('/order/{order:uuid}/success', fn(Order $order) => view('order.order-success', compact('order')))
    ->name('order.success');

/*
|--------------------------------------------------------------------------
| USER TICKETS ROUTES (Auth User ONLY)
|--------------------------------------------------------------------------*/
Route::middleware(['auth'])
    ->prefix('tickets')
    ->name('tickets.')
    ->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/{uuid}', [TicketController::class, 'show'])->name('show');
        Route::get('/{uuid}/download', [TicketController::class, 'downloadPdf'])->name('download');
    });

/*
|--------------------------------------------------------------------------
| SCANNER ROUTES (Scanner ONLY) — Validation QR uniquement
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:scanner'])
    ->prefix('scanner')
    ->name('scanner.')
    ->group(function () {
        // QR Scan & Validation - accès exclusif pour le rôle scanner
        Route::get('/qr/scan', [QRScanController::class, 'scan'])->name('qr.scan');
        Route::post('/qr/validate', [QRScanController::class, 'validateTicket'])->name('qr.validate');
    });

/*
|--------------------------------------------------------------------------
| STAFF ROUTES (Admin + Organizer ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,organizer'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        // QR Scan & Validation
        Route::get('/qr/scan', [QRScanController::class, 'scan'])->name('qr.scan');
        Route::post('/qr/validate', [QRScanController::class, 'validateTicket'])->name('qr.validate');
        Route::get('/qr/recent-scans', [QRScanController::class, 'recentScans'])->name('qr.recent-scans');

        // Gestion Commandes & Paiements
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [OrderManagementController::class, 'exportCsv'])->name('orders.export');

        // Dashboard Live
        Route::get('/dashboard', [LiveDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [LiveDashboardController::class, 'stats'])->name('dashboard.stats');
    });

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Admin ONLY) — Configuration & Gestion
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard admin (vue globale + stats)
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Configuration de l'événement unique (UPDATE ONLY — pas de CRUD)
        Route::get('/event/config', [EventConfigController::class, 'edit'])->name('event.config');
        Route::put('/event/config', [EventConfigController::class, 'update'])->name('event.update');

        // Gestion des commandes (liste, filtres, recherche)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

        // Gestion des acheteurs (liste utilisateurs + guests)
        // Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
    });


/*
|--------------------------------------------------------------------------
| Google Socialite
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [GoogleAuthController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
