<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation de Paiement - {{ $order->event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Simulation de Paiement</h1>
            <p class="text-gray-600">Environnement de développement</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-blue-800 mb-2">Détails de la commande</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Événement:</span>
                    <span class="font-medium">{{ $order->event->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Quantité:</span>
                    <span class="font-medium">{{ $order->quantity }} billet(s)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Montant total:</span>
                    <span class="font-medium text-green-600">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Transaction ID:</span>
                    <span class="font-mono text-xs">{{ $order->transaction_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Statut:</span>
                    <span class="px-2 py-1 rounded text-xs font-medium 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 mb-3">Méthode de paiement</h3>
            <div class="flex flex-wrap gap-2">
                @php
                    $methods = [
                        'cinetpay' => ['name' => 'CinetPay', 'color' => 'bg-orange-500'],
                        'fedapay' => ['name' => 'FedaPay', 'color' => 'bg-purple-500'],
                        'orange_money' => ['name' => 'Orange Money', 'color' => 'bg-orange-600'],
                        'mtn_money' => ['name' => 'MTN Money', 'color' => 'bg-yellow-500'],
                        'moov_money' => ['name' => 'Moov Money', 'color' => 'bg-blue-600'],
                    ];
                @endphp
                @foreach($methods as $key => $method)
                    <span class="{{ $method['color'] }} text-white px-3 py-1 rounded text-sm">
                        {{ $method['name'] }}
                    </span>
                @endforeach
            </div>
        </div>

        @if($order->isPending())
            <div class="space-y-3">
                <button onclick="simulatePayment('success')" 
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simuler un paiement RÉUSSI
                </button>

                <button onclick="simulatePayment('failed')" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Simuler un paiement ÉCHOUÉ
                </button>

                <button onclick="simulatePayment('cancelled')" 
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Simuler une ANNULATION
                </button>
            </div>
        @elseif($order->isConfirmed())
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <svg class="w-16 h-16 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="font-bold text-green-800 text-lg mb-1">Paiement Confirmé !</h3>
                <p class="text-green-700 text-sm mb-3">Les tickets ont été générés et envoyés par email.</p>
                <div class="text-xs text-green-600">
                    Payé le: {{ $order->paid_at->format('d/m/Y H:i') }}
                </div>
            </div>
        @elseif($order->isFailed())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="font-bold text-red-800 text-lg mb-1">Paiement Échoué</h3>
                <p class="text-red-700 text-sm">Vous pouvez réessayer ou contacter le support.</p>
            </div>
        @endif

        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('event.show') }}" 
                class="block text-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Retour à l'événement
            </a>
        </div>
    </div>

    <script>
        function simulatePayment(action) {
            const orderId = {{ $order->id }};
            
            fetch(`/api/payment/simulate-success/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Une erreur est survenue');
            });
        }
    </script>
</body>
</html>
