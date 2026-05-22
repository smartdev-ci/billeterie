@extends('layouts.public')

@section('title', 'Simulation de Paiement')

@section('content')
    <x-public.navbar />

    <main class="relative min-h-screen pt-24 pb-16">
        <div class="container mx-auto px-4">

            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold font-bebas-neue mb-4">
                    Simulation de Paiement en ligne
                </h1>
                <p class="text-gray-400 text-lg">
                    Testez le flux de paiement sans effectuer de transaction réelle
                </p>
            </div>

            <div class="max-w-2xl mx-auto space-y-6">

                {{-- Détails commande --}}
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8">
                    <h2 class="text-2xl font-bold font-space-grotesk mb-6 flex items-center gap-3">
                        <i class="fas fa-receipt text-orange-500"></i>
                        Détails de la commande
                    </h2>

                    <div class="bg-orange-500/10 border border-orange-500/20 rounded-xl p-4 mb-6 flex gap-3 text-sm text-gray-300">
                        <i class="fas fa-info-circle text-orange-500 mt-0.5 shrink-0"></i>
                        <span><strong class="text-orange-500">Mode Simulation :</strong> Cette page est utilisée pour tester le flux de paiement sans effectuer de transaction réelle.</span>
                    </div>

                    <div class="divide-y divide-white/5">
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-400 text-sm">Numéro de commande</span>
                            <span class="font-bold">#{{ $order->uuid }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-400 text-sm">Montant total</span>
                            <span class="text-xl font-bold text-orange-500">
                                {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-400 text-sm">Statut</span>
                            <span @class([
                                'px-3 py-1 rounded-full text-xs font-semibold',
                                'bg-yellow-500/15 text-yellow-400 border border-yellow-500/25' => $order->payment_status === 'pending',
                                'bg-green-500/15 text-green-400 border border-green-500/25'  => $order->payment_status === 'paid',
                                'bg-red-500/15 text-red-400 border border-red-500/25'        => $order->payment_status === 'failed',
                            ])>
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Simulation --}}
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8">
                    <h2 class="text-2xl font-bold font-space-grotesk mb-2 flex items-center gap-3">
                        <i class="fas fa-mobile-alt text-orange-500"></i>
                        Simuler une réponse de paiement
                    </h2>
                    <p class="text-gray-500 text-sm mb-6">
                        Choisissez le résultat à simuler pour tester le comportement de l'application.
                    </p>

                    <form action="{{ route('checkout.simulate.success') }}" method="POST" class="space-y-3">
                        @csrf

                        <button type="submit"
                            class="w-full py-4 px-6 bg-green-600 hover:bg-green-700 text-white font-bold font-space-grotesk text-base rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-green-500/20 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Simuler un paiement réussi
                        </button>
                    </form>

                    <form action="{{ route('checkout.simulate.failure') }}" method="POST" class="space-y-3 mt-3">
                        @csrf

                        <button type="submit"
                            class="w-full py-4 px-6 bg-red-600 hover:bg-red-700 text-white font-bold font-space-grotesk text-base rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-red-500/20 flex items-center justify-center gap-2">
                            <i class="fas fa-times-circle"></i>
                            Simuler un échec de paiement
                        </button>
                    </form>

                    <form action="{{ route('checkout.simulate.pending') }}" method="POST" class="space-y-3 mt-3">
                        @csrf

                        <button type="submit"
                            class="w-full py-4 px-6 bg-yellow-600 hover:bg-yellow-700 text-white font-bold font-space-grotesk text-base rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-yellow-500/20 flex items-center justify-center gap-2">
                            <i class="fas fa-clock"></i>
                            Simuler un paiement en attente
                        </button>
                    </form>
                </div>

                {{-- Retour --}}
                <a href="{{ route('checkout.show', $order->uuid) }}"
                   class="w-full py-4 px-6 border border-white/10 hover:border-white/25 hover:bg-white/5 text-gray-400 hover:text-white font-semibold font-space-grotesk text-base rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Retour au checkout
                </a>

            </div>
        </div>
    </main>
@endsection