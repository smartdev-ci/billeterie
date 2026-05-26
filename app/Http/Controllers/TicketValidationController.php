<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketValidationController extends Controller
{
    /**
     * Valider un ticket via son token
     * Utilisé par le scanner PWA
     */
    public function validate(Request $request)
    {
        $request->validate([
            'ticket_token' => 'required|string|uuid',
        ]);

        $token = $request->input('ticket_token');
        
        // Trouver le ticket par son token
        $ticket = Ticket::where('ticket_token', $token)->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket non trouvé',
                'error_code' => 'NOT_FOUND'
            ], 404);
        }

        // Vérifier si le ticket est actif
        if ($ticket->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket non actif (statut: ' . $ticket->status . ')',
                'error_code' => 'NOT_ACTIVE',
                'ticket' => [
                    'number' => $ticket->ticket_number,
                    'status' => $ticket->status,
                    'event' => $ticket->event->title ?? 'N/A'
                ]
            ], 400);
        }

        // Vérifier si le ticket n'a pas déjà été utilisé
        if ($ticket->used_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket déjà utilisé',
                'error_code' => 'ALREADY_USED',
                'ticket' => [
                    'number' => $ticket->ticket_number,
                    'used_at' => $ticket->used_at->format('d/m/Y H:i'),
                    'scanned_by' => $ticket->scannedBy ? $ticket->scannedBy->name : 'Inconnu',
                    'event' => $ticket->event->title ?? 'N/A'
                ]
            ], 400);
        }

        // Vérifier que l'événement existe et est actif
        if (!$ticket->event || !$ticket->event->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Événement inexistant ou inactif',
                'error_code' => 'EVENT_INACTIVE',
                'ticket' => [
                    'number' => $ticket->ticket_number,
                    'event' => $ticket->event->title ?? 'N/A'
                ]
            ], 400);
        }

        // Marquer le ticket comme utilisé
        $ticket->update([
            'status' => 'used',
            'used_at' => now(),
            'scanned_by' => Auth::id() ?? null
        ]);

        Log::info('Ticket validé avec succès', [
            'ticket_number' => $ticket->ticket_number,
            'ticket_token' => $ticket->ticket_token,
            'scanned_by' => Auth::id(),
            'event' => $ticket->event->title
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket valide - Accès autorisé',
            'ticket' => [
                'number' => $ticket->ticket_number,
                'holder' => $ticket->order->customer_name ?? 'N/A',
                'event' => $ticket->event->title,
                'used_at' => $ticket->used_at->format('d/m/Y H:i'),
                'scanned_by' => Auth::user()->name ?? 'Scanner'
            ]
        ], 200);
    }

    /**
     * Vérifier un ticket sans le marquer comme utilisé (pré-validation)
     */
    public function check(Request $request)
    {
        $request->validate([
            'ticket_token' => 'required|string|uuid',
        ]);

        $token = $request->input('ticket_token');
        $ticket = Ticket::where('ticket_token', $token)->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket non trouvé',
                'error_code' => 'NOT_FOUND'
            ], 404);
        }

        $isValid = $ticket->status === 'active' && $ticket->used_at === null;
        $eventValid = $ticket->event && $ticket->event->is_active;

        return response()->json([
            'success' => $isValid && $eventValid,
            'message' => $isValid && $eventValid ? 'Ticket valide' : 'Ticket invalide',
            'ticket' => [
                'number' => $ticket->ticket_number,
                'status' => $ticket->status,
                'used_at' => $ticket->used_at?->format('d/m/Y H:i'),
                'event' => $ticket->event->title ?? 'N/A',
                'holder' => $ticket->order->customer_name ?? 'N/A'
            ]
        ], 200);
    }

    /**
     * Historique des scans pour un utilisateur (scanner)
     */
    public function scanHistory(Request $request)
    {
        $user = Auth::user();
        
        $tickets = Ticket::where('scanned_by', $user->id)
            ->with(['event', 'order'])
            ->orderBy('used_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }
}
