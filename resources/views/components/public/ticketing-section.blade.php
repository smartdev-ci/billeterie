@props(['price', 'earlyBirdRemaining', 'paymentMethods'])

<section class="py-20 bg-gradient-to-b from-black to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Content --}}
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">VIENT VIVRE TON EXPERIENCE</h2>
                <p class="text-white/60 mb-8">Paiement rapide via Orange Money, MTN, Wave ou carte bancaire.</p>

                {{-- Early Bird Card --}}
                <div
                    class="bg-gradient-to-r from-orange-600/20 to-orange-900/20 border border-orange-500/30 rounded-2xl p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-orange-400 font-semibold">EARLY BIRD</span>
                        <span class="text-sm text-white/60">{{ $earlyBirdRemaining }} places restantes</span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-white/80">
                            <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Acces general
                        </div>
                        <div class="flex items-center text-white/80">
                            <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            1 boisson offerte
                        </div>
                        <div class="flex items-center text-white/80">
                            <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Badge Early Bird
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold text-white">{{ number_format($price, 0, ',', ' ') }}</span>
                            <span class="text-white/60 ml-1">FCFA</span>
                        </div>
                        <a href="{{ route('checkout.show') }}"
                            class="px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-xl transition">
                            Reserver ce billet
                        </a>
                    </div>
                </div>
                {{-- Payment Methods --}}
                <div>
                    <p class="text-sm text-white/50 mb-3">PAIEMENTS ACCEPTES</p>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center px-4 py-2 bg-white/5 rounded-lg border border-white/10">
                            <i class="fa-solid fa-mobile-screen-button text-orange-400"></i>
                            <span class="ml-2 text-sm text-white/80">Orange Money</span>
                        </div>
                        <div class="flex items-center px-4 py-2 bg-white/5 rounded-lg border border-white/10">
                            <i class="fa-solid fa-mobile-screen-button text-yellow-400"></i>
                            <span class="ml-2 text-sm text-white/80">MTN Money</span>
                        </div>
                        <div class="flex items-center px-4 py-2 bg-white/5 rounded-lg border border-white/10">
                            <i class="fa-solid fa-bolt text-blue-400"></i>
                            <span class="ml-2 text-sm text-white/80">Wave</span>
                        </div>
                        <div class="flex items-center px-4 py-2 bg-white/5 rounded-lg border border-white/10">
                            <i class="fa-regular fa-credit-card text-gray-300"></i>
                            <span class="ml-2 text-sm text-white/80">Carte Bancaire</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Visual --}}
            <div class="relative">
                <div
                    class="aspect-square rounded-3xl overflow-hidden border border-white/10 bg-gradient-to-br from-orange-900/30 to-purple-900/30">
                    <img src="{{ asset('images/ticket-preview.jpg') }}" alt="Apercu du billet"
                        class="w-full h-full object-cover opacity-80" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
                <div
                    class="absolute -bottom-4 -right-4 bg-black/80 backdrop-blur-sm border border-white/10 rounded-2xl p-4 max-w-xs">
                    <p class="text-sm text-white/70">Ton billet QR est securise et envoye immediatement apres paiement.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>