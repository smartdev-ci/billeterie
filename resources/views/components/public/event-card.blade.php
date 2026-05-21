@props(['event'])

<article 
    class="bg-[var(--black-card)] border border-[var(--border)] rounded-[var(--radius-lg)] overflow-hidden transition duration-300 hover:border-[var(--border-active)] hover:-translate-y-1 hover:shadow-[0_16px_48px_rgba(0,0,0,0.5)] {{ $event->status === 'upcoming' ? 'border-[var(--orange)]/20' : '' }}"
>
    {{-- Image Section --}}
    <div class="relative aspect-[16/9] overflow-hidden">
        <img 
            src="{{ $event->img }}" 
            alt="{{ $event->title }}" 
            class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
            loading="lazy"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[var(--black)]/80 to-transparent"></div>
        
        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex gap-2">
            @if($event->status === 'upcoming')
                <span class="inline-flex items-center gap-1.5 text-[0.7rem] font-bold tracking-[0.06em] uppercase px-2.5 py-1.25 rounded-[var(--radius-xl)] bg-[var(--orange)]/15 border border-[var(--orange)] text-[var(--orange)]">
                    <span class="w-1.5 h-1.5 bg-[var(--orange)] rounded-full animate-pulse-glow"></span>
                    Bientôt
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 text-[0.7rem] font-bold tracking-[0.06em] uppercase px-2.5 py-1.25 rounded-[var(--radius-xl)] bg-white/8 border border-[var(--border)] text-[var(--white-dim)]">
                    Passé
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 text-[0.7rem] font-bold tracking-[0.06em] uppercase px-2.5 py-1.25 rounded-[var(--radius-xl)] bg-[var(--violet)]/15 border border-[var(--violet)] text-[var(--violet)]">
                {{ $event->edition }}
            </span>
        </div>

        {{-- Tickets Progress Bar (Upcoming only) --}}
        @if($event->status === 'upcoming' && $event->ticketsLeft > 0)
            <div class="absolute bottom-0 left-0 right-0 px-3 pb-2.5 flex flex-col gap-1.25">
                <div class="h-0.75 bg-white/15 rounded-[2px] overflow-hidden">
                    <div 
                        class="h-full bg-gradient-to-r from-[var(--orange)] to-[var(--violet)] rounded-[2px] transition-all duration-1000"
                        style="width: {{ (($event->totalTickets - $event->ticketsLeft) / $event->totalTickets) * 100 }}%"
                    ></div>
                </div>
                <span class="text-[0.7rem] font-semibold text-[var(--orange)] tracking-[0.04em]">
                    {{ $event->ticketsLeft }} places restantes
                </span>
            </div>
        @endif
    </div>

    {{-- Content Section --}}
    <div class="p-5 flex flex-col gap-3">
        
        {{-- Tags --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach($event->tags as $tag)
                <span class="text-[0.7rem] font-semibold px-2.5 py-1 rounded-[var(--radius-xl)] bg-[var(--white-faint)] text-[var(--white-dim)] tracking-[0.04em]">
                    {{ $tag }}
                </span>
            @endforeach
        </div>

        {{-- Title --}}
        <h3 class="font-[var(--font-display)] text-[1.8rem] tracking-[0.04em] text-[var(--white)] leading-none">
            {{ $event->title }}
        </h3>

        {{-- Meta Info --}}
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2 text-[0.85rem] text-[var(--white-dim)]">
                <i class="fa-regular fa-calendar text-[var(--orange)] w-[14px]"></i>
                <span>{{ $event->date }} · {{ $event->time }}</span>
            </div>
            <div class="flex items-center gap-2 text-[0.85rem] text-[var(--white-dim)]">
                <i class="fa-solid fa-location-dot text-[var(--orange)] w-[14px]"></i>
                <span>{{ $event->venue }}, {{ $event->city }}</span>
            </div>
        </div>

        {{-- Artists --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach($event->artists as $artist)
                <span class="text-[0.75rem] font-semibold px-3 py-1.25 rounded-[var(--radius-xl)] bg-[var(--violet)]/10 border border-[var(--violet)]/25 text-[#c49dff]">
                    {{ $artist }}
                </span>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between mt-1 pt-4 border-t border-[var(--border)]">
            <span class="font-bold text-[0.95rem] text-[var(--orange)]">
                {{ $event->price }}
            </span>
            @if($event->status === 'upcoming')
                <a href="{{ route('checkout.show') }}" class="btn-primary text-[0.85rem] px-5 py-2.5">
                    Réserver
                </a>
            @else
                <a href="#gallery" class="btn-secondary text-[0.85rem] px-5 py-2.5">
                    Voir les photos
                </a>
            @endif
        </div>
    </div>
</article>