<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::get('/event', [EventController::class, 'show']); // Get active event (no ID needed)
Route::get('/event/{id}', [EventController::class, 'show']); // Get specific event
Route::get('/event/{id}/available-tickets', [EventController::class, 'availableTickets']);

// Payment routes
Route::prefix('payment')->group(function () {
    // Initiate payment (requires auth)
    Route::post('/initiate', [PaymentController::class, 'initiate'])->middleware('auth:sanctum');
    
    // Check payment status
    Route::get('/status/{orderId}', [PaymentController::class, 'checkPaymentStatus']);
    
    // Webhooks (public - secured by signature verification)
    Route::post('/webhook/cinetpay', [PaymentController::class, 'cinetpayWebhook']);
    Route::post('/webhook/fedapay', [PaymentController::class, 'fedapayWebhook']);
    
    // Simulation routes (local development only)
    Route::get('/simulation/{orderId}', [PaymentController::class, 'simulationPage'])->name('payment.simulation');
    Route::post('/simulate-success/{orderId}', [PaymentController::class, 'simulateSuccess']);
});

// Protected routes (Organizer/Admin only)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('events', EventController::class);
});
