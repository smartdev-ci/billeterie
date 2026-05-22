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
            ]);\n        }

        if ($requestedQuantity > $event->availableTickets()) {
            throw ValidationException::withMessages([
                'quantity' => "Seulement {$event->availableTickets()} billet(s) disponible(s)."
            ]);\n        }

        // Verrouillage pessimiste pour éviter la survente concurrente
        $event = $event->lockForUpdate()->first();
        if ($event->isSoldOut() || $requestedQuantity > $event->availableTickets()) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock insuffisant au moment du paiement. Réessayez.'
            ]);\n        }
    }

    /**
     * Libérer un quota réservé (en cas d'échec de paiement ou expiration)
     */
    public function releaseReservedQuota(int $quantity): void
    {
        // Dans cette implémentation simple, le quota est géré par la différence
        // entre capacity et les tickets vendus/validés
        // Cette méthode pourrait être étendue pour gérer des réservations temporaires
        // avec un système de timeout
    }

    public function confirmSale(Order $order, int $quantity): void
    {
        // Cette méthode est dépréciée - la génération des tickets se fait dans CheckoutController
        // avec QR code et signature appropriés
        throw new \LogicException('confirmSale() est dépréciée. Utilisez CheckoutController::generateTickets()');
    }
}
