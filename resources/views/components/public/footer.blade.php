<footer class="bg-black border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div class="md:col-span-2">
                <span class="text-xl font-bold text-white tracking-wider">LE PETIT POTO</span>
                <p class="mt-4 text-white/60 max-w-md">La soiree la plus electrique d'Abidjan. Rejoins le mouvement.</p>
                
                {{-- Newsletter --}}
                <form class="mt-6 flex gap-2">
                    <input 
                        type="email" 
                        placeholder="ton.email@exemple.com" 
                        class="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:border-orange-500 transition"
                    >
                    <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-lg transition">
                        S'inscrire
                    </button>
                </form>
                <p class="mt-2 text-xs text-white/40">Sois le premier a savoir. Des annonces des prochaines editions avant tout le monde.</p>
            </div>

            {{-- Navigation --}}
            <div>
                <h4 class="text-white font-semibold mb-4">NAVIGATION</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-white/60 hover:text-orange-400 transition text-sm">Accueil</a></li>
                    <li><a href="#events" class="text-white/60 hover:text-orange-400 transition text-sm">Evenements</a></li>
                    <li><a href="{{ route('checkout.show') }}" class="text-white/60 hover:text-orange-400 transition text-sm">Billetterie</a></li>
                    <li><a href="#gallery" class="text-white/60 hover:text-orange-400 transition text-sm">Galerie</a></li>
                    <li><a href="#faq" class="text-white/60 hover:text-orange-400 transition text-sm">FAQ</a></li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h4 class="text-white font-semibold mb-4">LEGAL</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-white/60 hover:text-orange-400 transition text-sm">Conditions generales</a></li>
                    <li><a href="#" class="text-white/60 hover:text-orange-400 transition text-sm">Politique de remboursement</a></li>
                    <li><a href="#" class="text-white/60 hover:text-orange-400 transition text-sm">Confidentialite</a></li>
                    <li><a href="#" class="text-white/60 hover:text-orange-400 transition text-sm">Contact</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-white/40 text-sm">© {{ date('Y') }} Le Petit Poto. Tous droits reserves.</p>
            <p class="text-white/40 text-sm">Fait avec passion a Abidjan, Cote d'Ivoire</p>
        </div>
    </div>
</footer>