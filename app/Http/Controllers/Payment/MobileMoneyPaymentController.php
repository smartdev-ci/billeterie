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

    // Page de simulation (dev/test uniquement)
    public function showSimulation(Order $order)
    {
        if ($order->payment_status !== 'pending') {
            abort(404, 'Commande invalide ou déjà traitée.');
        }
        
        // En production, cette route ne devrait pas être accessible
        if (app()->environment('production')) {
            abort(404, 'Page de simulation non disponible en production.');
        }
        
        return view('payment.simulate', compact('order'));
    }

    // Webhook pour les vrais paiements (OM/MTN)
    public function webhook(Request $request)
    {
        // Validation basique - à adapter selon le provider
        $validated = $request->validate([
            'status' => 'required|in:success,failed,pending',
            'reference' => 'required|string',
            'order_uuid' => 'required|uuid|exists:orders,uuid',
        ]);

        $order = Order::where('uuid', $validated['order_uuid'])->firstOrFail();
        
        $this->paymentService->processCallback($order, [
            'status' => $validated['status'],
            'payment_reference' => $validated['reference'],
        ]);

        return response()->json(['status' => 'processed']);
    }

    // Webhook callback simulé (pour dev/test)
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