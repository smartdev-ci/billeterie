<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseTicketRequest;
use App\Models\Order;
use App\Services\EventService;
use App\Services\Payment\PaymentService;
use App\Services\Ticket\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private PaymentService $paymentService,
        private TicketService $ticketService
    ) {}

    public function show(Request $request): View
    {
        return view('order.checkout');
    }

    public function store(PurchaseTicketRequest $request): RedirectResponse
    {
        $this->eventService->checkAndReserveQuota($request->quantity);

        $order = DB::transaction(function () use ($request) {
            return Order::create([
                'user_id'         => auth()->id(),
                'customer_name'   => $request->customer_name,
                'customer_email'  => $request->customer_email,
                'customer_phone'  => $request->customer_phone,
                'quantity'        => $request->quantity,
                'total_amount'    => Order::calculateTotal($request->quantity),
                'mobile_provider' => $request->mobile_provider,
                'payment_status'  => 'pending',
            ]);
        });

        $paymentData = $this->paymentService->initiate($order);

        return redirect($paymentData['payment_url'])->with('order_uuid', $order->uuid);
    }

    /**
     * Simuler un paiement réussi
     */
    public function simulateSuccess(Request $request): RedirectResponse
    {
        $orderUuid = session('order_uuid');
        
        if (!$orderUuid) {
            return redirect()->route('checkout.show')
                ->with('error', 'Aucune commande en cours. Veuillez d\'abord initier un achat.');
        }

        $order = Order::where('uuid', $orderUuid)->firstOrFail();

        // Mettre à jour le statut de paiement
        $order->update(['payment_status' => 'completed']);

        // Générer les tickets
        $this->ticketService->generateForOrder($order);

        // Libérer le quota réservé (déjà consommé par la vente)
        $this->eventService->releaseReservedQuota($order->quantity);

        return redirect()->route('order.success', $order)
            ->with('success', 'Paiement réussi ! Vos billets ont été générés et envoyés par email.');
    }

    /**
     * Simuler un échec de paiement
     */
    public function simulateFailure(Request $request): RedirectResponse
    {
        $orderUuid = session('order_uuid');
        
        if (!$orderUuid) {
            return redirect()->route('checkout.show')
                ->with('error', 'Aucune commande en cours.');
        }

        $order = Order::where('uuid', $orderUuid)->firstOrFail();

        // Annuler la commande
        $order->update(['payment_status' => 'failed']);

        // Libérer le quota réservé
        $this->eventService->releaseReservedQuota($order->quantity);

        return redirect()->route('checkout.show')
            ->with('error', 'Le paiement a échoué. Votre réservation a été annulée.');
    }

    /**
     * Simuler un paiement en attente
     */
    public function simulatePending(Request $request): RedirectResponse
    {
        $orderUuid = session('order_uuid');
        
        if (!$orderUuid) {
            return redirect()->route('checkout.show')
                ->with('error', 'Aucune commande en cours.');
        }

        $order = Order::where('uuid', $orderUuid)->firstOrFail();

        // Garder la commande en attente (rien à changer)
        // Le quota reste réservé jusqu'à expiration ou confirmation

        return redirect()->route('checkout.show')
            ->with('info', 'Le paiement est en attente. Votre réservation est maintenue.');
    }
}