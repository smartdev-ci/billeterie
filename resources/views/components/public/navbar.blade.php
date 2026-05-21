<nav x-data="{ mobileOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-white tracking-wider">LE PETIT POTO</span>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-sm font-medium text-white/90 hover:text-orange-400 transition">Accueil</a>
                <a href="#events" class="text-sm font-medium text-white/70 hover:text-orange-400 transition">Evenements</a>
                <a href="{{ route('checkout.show') }}" class="text-sm font-medium text-white/70 hover:text-orange-400 transition">Billetterie</a>
                <a href="#gallery" class="text-sm font-medium text-white/70 hover:text-orange-400 transition">Galerie</a>
                <a href="#faq" class="text-sm font-medium text-white/70 hover:text-orange-400 transition">FAQ</a>
            </div>

            {{-- CTA Button --}}
            <div class="hidden md:block">
                <a href="{{ route('checkout.show') }}" class="inline-flex items-center px-5 py-2 bg-orange-600 hover:bg-orange-500 text-white text-sm font-semibold rounded-lg transition shadow-lg shadow-orange-600/25">
                    Reserver
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-white p-2" aria-label="Menu">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-transition class="md:hidden bg-black/95 border-t border-white/10">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('home') }}" class="block text-sm font-medium text-white/90 hover:text-orange-400 py-2">Accueil</a>
            <a href="#events" class="block text-sm font-medium text-white/70 hover:text-orange-400 py-2">Evenements</a>
            <a href="{{ route('checkout.show') }}" class="block text-sm font-medium text-white/70 hover:text-orange-400 py-2">Billetterie</a>
            <a href="#gallery" class="block text-sm font-medium text-white/70 hover:text-orange-400 py-2">Galerie</a>
            <a href="#faq" class="block text-sm font-medium text-white/70 hover:text-orange-400 py-2">FAQ</a>
            <a href="{{ route('checkout.show') }}" class="block w-full text-center px-5 py-3 bg-orange-600 hover:bg-orange-500 text-white text-sm font-semibold rounded-lg transition mt-4">
                Reserver maintenant
            </a>
        </div>
    </div>
</nav>