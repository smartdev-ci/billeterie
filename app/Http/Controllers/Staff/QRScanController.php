<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 
use App\Http\Requests\QRValidationRequest;
use App\Models\Ticket;
use App\Models\AdminAuditLog;
use App\Services\QR\QRCodeService;
use App\Services\Ticket\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class QRScanController extends Controller
{
    use AuthorizesRequests; 

    public function __construct(
        private TicketService $ticketService,
        private QRCodeService $qrService
    ) {}

    public function scan(): View
    {
        $this->authorize('validate', Ticket::class);
        return view('staff.qr.scan');
    }

    public function validateTicket(QRValidationRequest $request): JsonResponse
    {
        $this->authorize('validate', Ticket::class);

        $key = "qr_scan:{$request->ip()}";
        if (RateLimiter::tooManyAttempts($key, 15)) {
            AdminAuditLog::create([
                'action' => 'rate_limit.triggered',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json(['message' => 'Trop de tentatives. Patientez 60s.'], 429);
        }
        RateLimiter::hit($key, 60);

        if (! $this->qrService->validateSignature($request->ticket_uuid, $request->signature)) {
            AdminAuditLog::create([
                'action'     => 'ticket.signature_failed',
                'target_type' => 'ticket',
                'metadata'   => ['uuid' => $request->ticket_uuid],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json(['message' => 'Signature invalide.'], 403);
        }

        try {
            $result = $this->ticketService->validateAndMarkUsed(
                $request->ticket_uuid,
                $request->user(),
                ['ip' => $request->ip(), 'ua' => $request->userAgent()]
            );
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}