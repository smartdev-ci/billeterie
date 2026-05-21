<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventService
{
    public function checkAndReserveQuota(int $requestedQuantity): void
    {
        $event = Event::current() ?? throw ValidationException::withMessages([
            'event' => 'Aucun événement actif n\'est configuré.'
        ]);

        if ($event->isSoldOut()) {
            throw ValidationException::withMessages([
                'quantity' => 'L\'événement est complet.'
            ]);
        }

        if ($requestedQuantity > $event->availableTickets()) {
            throw ValidationException::withMessages([
                'quantity' => "Seulement {$event->availableTickets()} billet(s) disponible(s)."
            ]);
        }

        // Verrouillage pessimiste pour éviter la survente concurrente
        $event = $event->lockForUpdate()->first();
        if ($event->isSoldOut() || $requestedQuantity > $event->availableTickets()) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock insuffisant au moment du paiement. Réessayez.'
            ]);
        }
    }

    public function confirmSale(Order $order, int $quantity): void
    {
        $event = Event::current();

        DB::transaction(function () use ($event, $order, $quantity) {
            $event->increment('tickets_sold', $quantity);

            foreach (range(1, $quantity) as $i) {
                Ticket::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'event_id' => $event->id,
                    'customer_email' => $order->customer_email,
                    'qr_code' => '', // Généré par QRService au Sprint 4
                    'qr_signature' => '', // Signé au Sprint 4
                    'status' => 'valid',
                ]);
            }

            if ($event->isSoldOut()) {
                $event->update(['status' => 'sold_out']);
            }
        });
        // ... à la fin de la transaction
        app(\App\Services\Analytics\AnalyticsService::class)->clearCache();
    }
}
