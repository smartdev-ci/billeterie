<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\TicketPurchasedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTicketEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        Mail::to($this->order->customer_email)->send(new TicketPurchasedMail($this->order));
    }
}