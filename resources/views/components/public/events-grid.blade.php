@props(['events'])

<section id="events" class="py-24 sm:py-32 bg-[var(--black)]">
    <div class="container mx-auto px-6">
        
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <span class="section-label">Événements</span>
            <h2 class="section-title">Les Éditions du<br /><span class="text-[var(--orange)]">Petit Poto</span></h2>
            <p class="text-[var(--white-dim)] max-w-[460px] mx-auto mt-4 text-base">
                Chaque édition est une expérience unique. Ne rate pas la prochaine.
            </p>
        </div>

        {{-- Events Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <x-public.event-card :event="$event" />
            @endforeach
        </div>
    </div>
</section>