<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketPurchasedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Votre billet Le Petit Poto  #{$this->order->uuid}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.ticket-purchased');
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.ticket', ['order' => $this->order])->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn () => $pdf->output(), "ticket-{$this->order->uuid}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}