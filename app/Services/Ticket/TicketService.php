<?php

namespace App\Services\Ticket;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\AdminAuditLog;
use App\Services\QR\QRCodeService;
use App\Jobs\SendTicketEmailJob;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(private QRCodeService $qrService) {}

    public function generateForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            for ($i = 0; $i < $order->quantity; $i++) {
                $ticket = Ticket::create([
                    'order_id'       => $order->id,
                    'user_id'        => $order->user_id,
                    'event_id'       => 1,
                    'customer_email' => $order->customer_email,
                    'status'         => 'valid',
                ]);

                $ticket->update([
                    'qr_code'      => $this->qrService->generate($ticket->uuid),
                    'qr_signature' => $this->qrService->sign($ticket->uuid),
                ]);
            }
        });

        // Envoi asynchrone pour ne pas bloquer le callback
        dispatch(new SendTicketEmailJob($order))->onQueue('emails');
    }

    public function validateAndMarkUsed(string $uuid, User $validator, array $requestContext): array
    {
        // Provide explicit operator and boolean to satisfy method signature
        $ticket = Ticket::where('uuid', '=', $uuid, 'and')->firstOrFail();

        if (! $ticket->isUsable()) {
            throw new \RuntimeException("Ce billet est déjà {$ticket->status}.");
        }

        return DB::transaction(function () use ($ticket, $validator, $requestContext) {
            $ticket->update([
                'status'       => 'used',
                'validated_by' => $validator->id,
                'used_at'      => now(),
            ]);

            AdminAuditLog::create([
                'user_id'    => $validator->id,
                'action'     => 'ticket.validated',
                'target_type' => 'ticket',
                'target_id'  => $ticket->id,
                'metadata'   => ['event_id' => $ticket->event_id, 'order_uuid' => $ticket->order->uuid],
                'ip_address' => $requestContext['ip'],
                'user_agent' => $requestContext['ua'],
            ]);

            return [
                'status'  => 'success',
                'message' => 'Billet validé',
                'ticket'  => $ticket->only(['uuid', 'customer_name', 'customer_email']),
            ];
            
        });
        
    }
}
