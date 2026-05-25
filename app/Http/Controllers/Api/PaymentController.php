<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class PaymentController extends Controller
{
    /**
     * Initialiser un paiement Mobile Money
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|in:cinetpay,fedapay,orange_money,mtn_money,moov_money',
        ]);

        $event = Event::findOrFail($request->event_id);

        // Vérifier la disponibilité des billets
        if ($event->available_tickets < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Nombre de billets insuffisant disponible.',
            ], 400);
        }

        // Calculer le montant total (prix fixe 5000 FCFA par billet)
        $totalAmount = $event->ticket_price * $request->quantity;

        // Créer la commande en statut pending
        $order = Order::create([
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'quantity' => $request->quantity,
            'total_amount' => $totalAmount,
            'status' => Order::STATUS_PENDING,
            'payment_method' => $request->payment_method,
            'transaction_id' => Order::generateTransactionId(),
        ]);

        // Initialiser le paiement selon la méthode choisie
        try {
            if ($request->payment_method === 'cinetpay') {
                $paymentData = $this->initiateCinetPay($order, $event);
            } elseif ($request->payment_method === 'fedapay') {
                $paymentData = $this->initiateFedaPay($order, $event);
            } else {
                // Pour Orange Money, MTN, Moov - via CinetPay ou FedaPay
                $paymentData = $this->initiateCinetPay($order, $event);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement initié avec succès',
                'data' => [
                    'order_id' => $order->id,
                    'transaction_id' => $order->transaction_id,
                    'amount' => $totalAmount,
                    'currency' => 'XOF',
                    'payment_url' => $paymentData['payment_url'] ?? null,
                    'payment_token' => $paymentData['token'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur initiation paiement: ' . $e->getMessage());
            
            $order->markAsFailed();
            
            return response()->json([
                'success' => false,
                'message' => 'Échec de l\'initialisation du paiement. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Webhook pour CinetPay
     */
    public function cinetpayWebhook(Request $request): JsonResponse
    {
        Log::info('Webhook CinetPay reçu', ['data' => $request->all()]);

        // Récupérer les données du webhook
        $transactionId = $request->input('cpm_trans_id');
        $status = $request->input('cpm_result');
        $orderId = $request->input('order_id');

        // Vérifier la signature (à implémenter avec la clé secrète CinetPay)
        // $isValidSignature = $this->verifyCinetPaySignature($request);

        if (!$orderId || !$transactionId) {
            return response()->json([
                'success' => false,
                'message' => 'Données webhook invalides',
            ], 400);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée',
            ], 404);
        }

        // Vérifier si le paiement est réussi (status = 00 pour CinetPay)
        if ($status === '00' || $status === 'ACCEPTED') {
            DB::transaction(function () use ($order, $transactionId) {
                // Marquer la commande comme confirmée
                $order->markAsConfirmed($transactionId);

                // Générer les tickets
                $this->generateTickets($order);
            });

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé avec succès',
            ]);
        } else {
            $order->markAsFailed();

            return response()->json([
                'success' => false,
                'message' => 'Paiement échoué ou annulé',
            ]);
        }
    }

    /**
     * Webhook pour FedaPay
     */
    public function fedapayWebhook(Request $request): JsonResponse
    {
        Log::info('Webhook FedaPay reçu', ['data' => $request->all()]);

        // Récupérer les données du webhook
        $transaction = $request->input('transaction');
        $orderId = $transaction['reference'] ?? null;

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Données webhook invalides',
            ], 400);
        }

        $order = Order::where('transaction_id', $orderId)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée',
            ], 404);
        }

        // Vérifier le statut du paiement
        $status = $transaction['status'] ?? '';

        if ($status === 'approved' || $status === 'completed') {
            DB::transaction(function () use ($order) {
                // Marquer la commande comme confirmée
                $order->markAsConfirmed($order->transaction_id);

                // Générer les tickets
                $this->generateTickets($order);
            });

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé avec succès',
            ]);
        } else {
            $order->markAsFailed();

            return response()->json([
                'success' => false,
                'message' => 'Paiement échoué ou annulé',
            ]);
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus($orderId): JsonResponse
    {
        $order = Order::with('event')->findOrFail($orderId);

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'transaction_id' => $order->transaction_id,
                'status' => $order->status,
                'amount' => $order->total_amount,
                'currency' => 'XOF',
                'paid_at' => $order->paid_at,
                'event' => [
                    'id' => $order->event->id,
                    'title' => $order->event->title,
                ],
            ],
        ]);
    }

    /**
     * Initialiser un paiement CinetPay
     */
    private function initiateCinetPay(Order $order, Event $event): array
    {
        // Configuration CinetPay (à mettre dans .env)
        $apiKey = config('services.cinetpay.api_key');
        $siteId = config('services.cinetpay.site_id');
        $platformId = config('services.cinetpay.platform_id');

        // En environnement de test, retourner une URL de simulation
        if (app()->environment('local')) {
            return [
                'payment_url' => route('payment.simulation', ['order_id' => $order->id]),
                'token' => 'SIMULATED-' . Uuid::uuid4()->toString(),
            ];
        }

        // Paramètres pour CinetPay
        $params = [
            'apikey' => $apiKey,
            'site_id' => $siteId,
            'transaction_id' => $order->transaction_id,
            'amount' => $order->total_amount,
            'currency' => 'XOF',
            'channels' => 'MOBILE_PAYMENT',
            'description' => "Achat de {$order->quantity} billet(s) pour {$event->title}",
            'customer_name' => $order->customer_name,
            'customer_surname' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone_number' => $order->customer_phone,
            'return_url' => route('payment.success'),
            'notify_url' => route('payment.webhook.cinetpay'),
        ];

        // Appel API CinetPay (à implémenter avec le SDK officiel)
        // $response = Http::post('https://api.cinetpay.com/v1/payment', $params);
        
        // Simulation pour le développement
        return [
            'payment_url' => "https://checkout.cinetpay.com?transaction_id={$order->transaction_id}",
            'token' => Uuid::uuid4()->toString(),
        ];
    }

    /**
     * Initialiser un paiement FedaPay
     */
    private function initiateFedaPay(Order $order, Event $event): array
    {
        // Configuration FedaPay (à mettre dans .env)
        $publicKey = config('services.fedapay.public_key');
        $secretKey = config('services.fedapay.secret_key');

        // En environnement de test, retourner une URL de simulation
        if (app()->environment('local')) {
            return [
                'payment_url' => route('payment.simulation', ['order_id' => $order->id]),
                'token' => 'SIMULATED-' . Uuid::uuid4()->toString(),
            ];
        }

        // Paramètres pour FedaPay
        $params = [
            'amount' => $order->total_amount,
            'currency' => 'XOF',
            'description' => "Achat de {$order->quantity} billet(s) pour {$event->title}",
            'metadata' => [
                'order_id' => $order->id,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
            ],
        ];

        // Appel API FedaPay (à implémenter avec le SDK officiel)
        // $response = Http::withHeaders(['Authorization' => 'Bearer ' . $secretKey])
        //     ->post('https://api.fedapay.com/v1/transactions', $params);

        // Simulation pour le développement
        return [
            'payment_url' => "https://checkout.fedapay.com?reference={$order->transaction_id}",
            'token' => Uuid::uuid4()->toString(),
        ];
    }

    /**
     * Générer les tickets après paiement confirmé
     */
    private function generateTickets(Order $order): void
    {
        $event = $order->event;
        $tickets = [];

        for ($i = 1; $i <= $order->quantity; $i++) {
            // Générer un token unique pour chaque ticket
            $token = Uuid::uuid4()->toString();

            // Générer un numéro de ticket (ex: LPP-ABC123-001)
            $ticketNumber = sprintf(
                'LPP-%s-%03d',
                strtoupper(substr($order->transaction_id, -6)),
                $i
            );

            // Générer l'URL du QR code via QRServer API
            $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$token}";

            $tickets[] = [
                'order_id' => $order->id,
                'event_id' => $event->id,
                'user_id' => $order->user_id,
                'ticket_token' => $token,
                'ticket_number' => $ticketNumber,
                'qr_image_path' => $qrImageUrl,
                'status' => Ticket::STATUS_ACTIVE,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insérer tous les tickets en une seule requête
        Ticket::insert($tickets);

        // Mettre à jour le nombre de billets vendus pour l'événement
        $event->increment('tickets_sold', $order->quantity);

        // Déclencher l'envoi des tickets par email
        // SendTicketEmail::dispatch($order);
    }

    /**
     * Page de simulation de paiement (pour développement local)
     */
    public function simulationPage($orderId): \Illuminate\View\View
    {
        $order = Order::with('event')->findOrFail($orderId);

        return view('payment.simulation', compact('order'));
    }

    /**
     * Simuler un paiement réussi (pour développement local)
     */
    public function simulateSuccess(Request $request, $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);

        DB::transaction(function () use ($order) {
            $order->markAsConfirmed($order->transaction_id);
            $this->generateTickets($order);
        });

        return response()->json([
            'success' => true,
            'message' => 'Paiement simulé avec succès',
            'data' => [
                'order_id' => $order->id,
                'tickets_count' => $order->tickets()->count(),
            ],
        ]);
    }
}
