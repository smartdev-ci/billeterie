<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ) {}

    /**
     * Liste des billets de l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();
        
        $tickets = Ticket::with(['order.event'])
            ->where('customer_email', $user->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Détail d'un billet spécifique
     */
    public function show(string $uuid)
    {
        $ticket = Ticket::with(['order.event'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Vérification d'accès : propriétaire ou admin/organizer
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Vous devez être connecté pour voir ce billet.');
        }

        if ($user->email !== $ticket->customer_email && 
            !$user->hasRole(['admin', 'organizer'])) {
            abort(403, 'Vous n\'avez pas accès à ce billet.');
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Télécharger le PDF du billet
     */
    public function downloadPdf(string $uuid)
    {
        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
        
        $pdfPath = "tickets/{$ticket->uuid}.pdf";
        
        if (!Storage::disk('public')->exists($pdfPath)) {
            // Générer le PDF s'il n'existe pas encore
            $this->ticketService->generatePdf($ticket);
        }

        return Storage::disk('public')->download($pdfPath, "billet-{$ticket->uuid}.pdf");
    }
}
