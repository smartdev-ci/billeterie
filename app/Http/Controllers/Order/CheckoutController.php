<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseTicketRequest;
use App\Models\Order;
use App\Services\EventService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private PaymentService $paymentService
    ) {}

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
}