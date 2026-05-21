<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Ticket\TicketService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(private TicketService $ticketService) {}

    public function initiate(Order $order): array
    {
        // Simulation d'initiation (prod : appel API OM/MTN)
        return [
            'payment_url' => route('payment.simulate', ['order' => $order]),
            'reference'   => 'PAY-' . strtoupper(uniqid()),
            'expires_at'  => now()->addMinutes(15),
        ];
    }

    public function processCallback(Order $order, array $payload): bool
    {
        return DB::transaction(function () use ($order, $payload) {
            if ($payload['status'] === 'success') {
                $order->markAsPaid($payload['payment_reference'], $order->mobile_provider);
                $this->ticketService->generateForOrder($order);
                Log::info("Paiement confirmé: {$order->uuid}");
                return true;
            }

            $order->update(['payment_status' => 'failed']);
            Log::warning("Paiement échoué: {$order->uuid}");
            return false;
        });
    }
}