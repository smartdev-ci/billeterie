@props(['event', 'countdownEnd'])

<section id="hero" class="relative min-h-screen flex items-center overflow-hidden bg-black">
    
    {{-- Background Image with Overlay --}}
    <div class="absolute inset-0 z-0">
        <img 
            src="https://images.pexels.com/photos/1763075/pexels-photo-1763075.jpeg?auto=compress&cs=tinysrgb&w=1920" 
            alt="Ambiance Le Petit Poto" 
            class="w-full h-full object-cover object-top brightness-[0.45]"
            loading="eager"
        >
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/10 to-black/85"></div>
    </div>

    {{-- Animated Glow Orbs --}}
    <div class="absolute top-[-200px] right-[-100px] w-[600px] h-[600px] bg-orange-500/30 rounded-full blur-[100px] opacity-35 animate-float" style="animation-delay: 0s"></div>
    <div class="absolute bottom-[-100px] left-[-150px] w-[500px] h-[500px] bg-violet-600/30 rounded-full blur-[100px] opacity-35 animate-float" style="animation-delay: -4s"></div>

    {{-- Content --}}
    <div class="relative z-10 container mx-auto px-4 pt-24 pb-20 flex flex-col items-start gap-6">
        
        {{-- Edition Badge --}}
        <div class="inline-flex items-center gap-2.5 bg-orange-500/12 border border-orange-500/30 text-orange-400 text-[0.8rem] font-semibold tracking-[0.08em] uppercase px-4 py-2 rounded-2xl animate-fade-in-up" style="animation-delay: 0s">
            <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse-glow"></span>
            Édition 5 — Le Grand Retour
        </div>

        {{-- Main Title --}}
        <h1 class="font-display text-[clamp(4rem,12vw,9rem)] tracking-[0.04em] leading-[0.95] text-white animate-fade-in-up" style="animation-delay: 0.1s">
            LE PAIYA<br />
            <span class="text-orange-500 block [-webkit-text-stroke:2px_rgba(249,115,22,1)] [text-stroke:2px_rgba(249,115,22,1)]">COMMENCE ICI</span>
        </h1>

        {{-- Subtitle --}}
        <p class="text-[clamp(0.95rem,2.5vw,1.1rem)] text-white/70 leading-relaxed max-w-[480px] animate-fade-in-up" style="animation-delay: 0.2s">
            La soirée la plus électrique d'Abidjan revient.<br />
            {{ $event->event_date->format('d F Y') }} · {{ $event->location }}
        </p>

        {{-- CTA Buttons --}}
        <div class="flex gap-4 flex-wrap animate-fade-in-up" style="animation-delay: 0.3s">
            <a href="{{ route('checkout.show') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-xl transition shadow-lg shadow-orange-600/25 transform hover:scale-[1.02]">
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M15 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Réserver maintenant
            </a>
            <a href="#events" class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/20 transition backdrop-blur-sm">
                Voir l'édition
            </a>
        </div>

        {{-- Countdown Timer --}}
        <div class="mt-2 animate-fade-in-up" style="animation-delay: 0.4s">
            <p class="text-[0.8rem] font-medium tracking-[0.1em] uppercase text-white/60 mb-3">Prochain événement dans</p>
            <x-public.countdown :target="$countdownEnd" />
        </div>

        {{-- Stats Row --}}
        <div class="flex items-center gap-6 mt-4 animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="flex flex-col gap-0.5">
                <span class="font-display text-2xl tracking-[0.05em] text-white">2 000+</span>
                <span class="text-[0.75rem] text-white/60 uppercase tracking-[0.08em]">participants</span>
            </div>
            <div class="w-px h-10 bg-white/10"></div>
            <div class="flex flex-col gap-0.5">
                <span class="font-display text-2xl tracking-[0.05em] text-white">12</span>
                <span class="text-[0.75rem] text-white/60 uppercase tracking-[0.08em]">DJs & artistes</span>
            </div>
            <div class="w-px h-10 bg-white/10"></div>
            <div class="flex flex-col gap-0.5">
                <span class="font-display text-2xl tracking-[0.05em] text-white">5</span>
                <span class="text-[0.75rem] text-white/60 uppercase tracking-[0.08em]">éditions</span>
            </div>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2 text-white/60 text-[0.7rem] tracking-[0.12em] uppercase animate-float hidden md:flex">
        <span>Scroll</span>
        <div class="text-orange-400">
            <svg class="w-4 h-5" viewBox="0 0 16 20" fill="none">
                <path d="M8 0v16M2 10l6 8 6-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</section>

@push('styles')
<style>
    /* Animations immersives */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    @keyframes pulseGlow {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
        50% { opacity: 0.8; box-shadow: 0 0 0 8px rgba(249, 115, 22, 0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.7s ease forwards;
        opacity: 0;
    }
    .animate-float {
        animation: float 8s ease-in-out infinite;
    }
    .animate-pulse-glow {
        animation: pulseGlow 2s ease-in-out infinite;
    }
    
    /* Responsive mobile */
    @media (max-width: 768px) {
        #hero .container {
            align-items: center;
            text-align: center;
            padding-top: 120px;
        }
        #hero .flex-wrap {
            justify-content: center;
        }
    }
</style>
@endpush