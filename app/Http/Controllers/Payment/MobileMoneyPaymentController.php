<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\MobileMoneyPaymentRequest;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class MobileMoneyPaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    // Page de simulation (dev/test)
    public function showSimulation(Order $order)
    {
        if ($order->payment_status !== 'pending') {
            abort(404, 'Commande invalide ou déjà traitée.');
        }
        return view('payment.simulate', compact('order'));
    }

    // Webhook callback simulé
    public function callback(MobileMoneyPaymentRequest $request)
    {
        $executed = RateLimiter::attempt("payment:{$request->order_uuid}", 3, function () use ($request) {
            $order = Order::where('uuid', $request->order_uuid)->firstOrFail();
            return $this->paymentService->processCallback($order, $request->validated());
        }, 60);

        if (!$executed) {
            abort(429, 'Trop de tentatives de validation.');
        }

        return redirect()->route('order.success', $request->order_uuid);
    }
}