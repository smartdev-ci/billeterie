<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail du Billet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- En-tête du billet -->
                    <div class="border-b pb-6 mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->order->event->name }}</h1>
                                <p class="text-lg text-gray-600 mt-1">{{ $ticket->ticket_type }}</p>
                            </div>
                            <span class="px-4 py-2 text-sm font-medium rounded-full 
                                @if($ticket->is_used)
                                    bg-red-100 text-red-800
                                @else
                                    bg-green-100 text-green-800
                                @endif">
                                @if($ticket->is_used)
                                    ✓ Utilisé
                                @else
                                    ✓ Valide
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Informations principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase">Événement</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $ticket->order->event->name }}</p>
                            <p class="text-gray-600">{{ $ticket->order->event->location }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase">Date & Heure</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $ticket->order->event->event_date->format('d/m/Y') }}
                            </p>
                            <p class="text-gray-600">
                                {{ $ticket->order->event->event_date->format('H:i') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase">Client</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $ticket->customer_name }}</p>
                            <p class="text-gray-600">{{ $ticket->customer_email }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase">Prix Payé</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ number_format($ticket->price, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="border-t pt-6 mb-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Votre QR Code</h3>
                        <div class="flex flex-col items-center">
                            <img src="data:image/png;base64,{{ base64_encode($ticket->qr_code) }}" 
                                 alt="QR Code du billet"
                                 class="w-64 h-64 border-4 border-gray-200 rounded-lg">
                            <p class="mt-4 text-sm text-gray-600 text-center">
                                Présentez ce QR code à l'entrée de l'événement
                            </p>
                        </div>
                    </div>

                    <!-- UUID du billet -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-600">ID du billet:</p>
                        <p class="font-mono text-sm text-gray-900 break-all">{{ $ticket->uuid }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-4">
                        <a href="{{ route('tickets.download', $ticket->uuid) }}" 
                           class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            📥 Télécharger le PDF
                        </a>
                        <a href="{{ route('tickets.index') }}" 
                           class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                            ← Retour à la liste
                        </a>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">ℹ️ Instructions</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Présentez ce billet (version papier ou numérique) à l'entrée</li>
                            <li>• Le QR code sera scanné pour valider votre accès</li>
                            <li>• Ce billet est nominatif et non transférable</li>
                            <li>• En cas de problème, contactez l'organisateur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
