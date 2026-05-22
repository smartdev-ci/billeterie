<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Billets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($tickets->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tickets as $ticket)
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="font-bold text-lg">{{ $ticket->order->event->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $ticket->ticket_type }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded 
                                            @if($ticket->is_used)
                                                bg-red-100 text-red-800
                                            @else
                                                bg-green-100 text-green-800
                                            @endif">
                                            @if($ticket->is_used)
                                                Utilisé
                                            @else
                                                Valide
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                                        <p><strong>Date:</strong> {{ $ticket->order->event->event_date->format('d/m/Y H:i') }}</p>
                                        <p><strong>Client:</strong> {{ $ticket->customer_name }}</p>
                                        <p><strong>Prix:</strong> {{ number_format($ticket->price, 0, ',', ' ') }} FCFA</p>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('tickets.show', $ticket->uuid) }}" 
                                           class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
                                            Voir
                                        </a>
                                        <a href="{{ route('tickets.download', $ticket->uuid) }}" 
                                           class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors">
                                            PDF
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $tickets->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun billet</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de billets.</p>
                            <div class="mt-6">
                                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Découvrir les événements
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
