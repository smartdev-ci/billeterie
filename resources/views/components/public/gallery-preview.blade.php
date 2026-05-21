<section id="gallery" class="py-20 bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">LE PAIVA EN IMAGES</h2>
            <p class="text-white/60">Tu etais la ? Reviens. Tu n'y etais pas ? Tu comprends maintenant.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @for($i = 1; $i <= 8; $i++)
                <div class="relative aspect-square rounded-xl overflow-hidden group cursor-pointer">
                    <img 
                        src="{{ asset("images/gallery/{$i}.jpg") }}" 
                        alt="Moment Le Petit Poto {{ $i }}" 
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </div>
                </div>
            @endfor
        </div>

        <div class="text-center mt-8">
            <a href="#" class="inline-flex items-center text-orange-400 hover:text-orange-300 font-semibold transition">
                Voir toute la galerie
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>