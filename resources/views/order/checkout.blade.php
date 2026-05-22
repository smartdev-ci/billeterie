@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <main class="relative min-h-screen pt-24 pb-16">
        <div class="container mx-auto px-4">
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold font-bebas-neue mb-4">
                    Réserver vos billets
                </h1>
                <p class="text-gray-400 text-lg">
                    Complétez le formulaire ci-dessous pour finaliser votre commande
                </p>
            </div>

            {{-- Checkout Form --}}
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Informations Client --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8">
                        <h2 class="text-2xl font-bold font-space-grotesk mb-6 flex items-center gap-3">
                            <i class="fas fa-user text-orange-500"></i>
                            Informations personnelles
                        </h2>

                        <div class="space-y-4">
                            {{-- Nom complet --}}
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-300 mb-2">
                                    Nom complet <span class="text-orange-500">*</span>
                                </label>
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       value="{{ old('customer_name') }}"
                                       required
                                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors"
                                       placeholder="Ex: Jean Kouassi">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-300 mb-2">
                                    Adresse email <span class="text-orange-500">*</span>
                                </label>
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       value="{{ old('customer_email') }}"
                                       required
                                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors"
                                       placeholder="Ex: jean@example.com">
                                @error('customer_email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-300 mb-2">
                                    Numéro de téléphone <span class="text-orange-500">*</span>
                                </label>
                                <input type="tel" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       value="{{ old('customer_phone') }}"
                                       required
                                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors"
                                       placeholder="Ex: 0701020304">
                                @error('customer_phone')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Quantité --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8">
                        <h2 class="text-2xl font-bold font-space-grotesk mb-6 flex items-center gap-3">
                            <i class="fas fa-ticket-alt text-orange-500"></i>
                            Nombre de billets
                        </h2>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-300 mb-2">
                                Quantité <span class="text-orange-500">*</span>
                            </label>
                            <select id="quantity" 
                                    name="quantity" 
                                    required
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors">
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('quantity') == $i ? 'selected' : '' }}>
                                        {{ $i }} billet{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prix total --}}
                        <div class="mt-6 p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300">Prix unitaire</span>
                                <span class="font-bold">5 000 FCFA</span>
                            </div>
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-orange-500/20">
                                <span class="text-lg font-bold text-orange-500">Total à payer</span>
                                <span class="text-2xl font-bold text-orange-500" id="total-amount">5 000 FCFA</span>
                            </div>
                        </div>
                    </div>

                    {{-- Paiement Mobile --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8">
                        <h2 class="text-2xl font-bold font-space-grotesk mb-6 flex items-center gap-3">
                            <i class="fas fa-mobile-alt text-orange-500"></i>
                            Paiement Mobile Money
                        </h2>

                        <div>
                            <label for="mobile_provider" class="block text-sm font-medium text-gray-300 mb-2">
                                Opérateur <span class="text-orange-500">*</span>
                            </label>
                            <select id="mobile_provider" 
                                    name="mobile_provider" 
                                    required
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors">
                                <option value="">Choisir un opérateur</option>
                                <option value="orange" {{ old('mobile_provider') === 'orange' ? 'selected' : '' }}>Orange Money</option>
                                <option value="mtn" {{ old('mobile_provider') === 'mtn' ? 'selected' : '' }}>MTN Mobile Money</option>
                                <option value="wave" {{ old('mobile_provider') === 'wave' ? 'selected' : '' }}>Wave</option>
                            </select>
                            @error('mobile_provider')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" 
                            class="w-full py-4 px-6 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold font-space-grotesk text-lg rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-orange-500/25">
                        <i class="fas fa-lock mr-2"></i>
                        Payer maintenant
                    </button>
                </form>
            </div>
        </div>
    </main>

    @push('scripts')
        <script>
            // Calcul du montant total en fonction de la quantité
            const quantitySelect = document.getElementById('quantity');
            const totalAmountElement = document.getElementById('total-amount');
            const unitPrice = 5000;

            function updateTotal() {
                const quantity = parseInt(quantitySelect.value);
                const total = quantity * unitPrice;
                totalAmountElement.textContent = total.toLocaleString('fr-FR') + ' FCFA';
            }

            quantitySelect.addEventListener('change', updateTotal);
            
            // Initialisation
            updateTotal();
        </script>
    @endpush
@endsection
