@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <main class="min-h-screen flex items-center justify-center px-4 py-24">
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-green-500/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative w-full max-w-lg text-center">
            {{-- Success Icon --}}
            <div class="w-24 h-24 rounded-full bg-green-500/20 border-2 border-green-500/40 flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <span class="inline-block text-xs font-bold tracking-[0.15em] uppercase text-green-400 mb-3">Paiement confirmé</span>
            <h1 class="font-display text-5xl tracking-[0.04em] text-white mb-4">C'EST DANS LA POCHE !</h1>
            <p class="text-white/60 mb-8">
                Ton billet a été généré et envoyé à <span class="text-orange-400 font-medium">{{ $order->customer_email }}</span>.<br>
                Vérifie ta boîte mail (et tes spams).
            </p>

            {{-- Order Details --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-8 text-left">
                <h3 class="text-sm font-bold tracking-[0.08em] uppercase text-white/50 mb-4">Détails de ta commande</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-white/60 text-sm">Référence</span>
                        <span class="text-white text-sm font-mono">{{ substr($order->uuid, 0, 8) }}...</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/60 text-sm">Nom</span>
                        <span class="text-white text-sm">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/60 text-sm">Quantité</span>
                        <span class="text-white text-sm">{{ $order->quantity }} billet{{ $order->quantity > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex justify-between border-t border-white/10 pt-3">
                        <span class="text-white font-semibold">Total payé</span>
                        <span class="text-orange-400 font-bold text-lg">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            {{-- Instructions --}}
            <div class="bg-orange-500/10 border border-orange-500/20 rounded-xl p-4 mb-8 text-left">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-orange-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-white/70">
                        <strong class="text-orange-400">Important :</strong> Présente le QR Code de ton ticket à l'entrée de l'événement. Chaque QR est unique et ne peut être utilisé qu'une seule fois.
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('home') }}" class="px-8 py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold rounded-xl transition">
                    Retour à l'accueil
                </a>
                <a href="{{ route('checkout.show') }}" class="px-8 py-3 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-xl transition shadow-lg shadow-orange-600/25">
                    Acheter d'autres billets
                </a>
            </div>
        </div>
    </main>
@endsection