@props(['questions'])

<section id="faq" class="py-24 sm:py-32 bg-[var(--black-soft)]">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.6fr] gap-16 lg:gap-20 items-start">
            
            {{-- Header Sticky (Desktop) --}}
            <div class="lg:sticky lg:top-24 flex flex-col gap-4">
                <span class="section-label">FAQ</span>
                <h2 class="section-title">Tu as des<br /><span class="text-[var(--orange)]">questions ?</span></h2>
                <p class="text-[0.95rem] text-[var(--white-dim)] leading-relaxed mt-1">
                    On a les réponses. Si tu ne trouves pas ce que tu cherches,<br />
                    contacte-nous sur WhatsApp.
                </p>
                <a href="https://wa.me/2250700000000" class="inline-flex items-center gap-2.5 bg-[#25d366]/10 border border-[#25d366]/30 text-[#25d366] font-semibold text-[0.88rem] px-5 py-3 rounded-[var(--radius-xl)] transition hover:bg-[#25d366]/18 hover:border-[#25d366] w-fit mt-2">
                    <i class="fa-brands fa-whatsapp"></i>
                    Nous contacter
                </a>
            </div>

            {{-- FAQ List --}}
            <div class="flex flex-col" x-data="{ open: null }">
                @foreach($questions as $index => $faq)
                    <div class="border-t border-[var(--border)] {{ $loop->first ? 'border-t-0' : '' }}">
                        <button 
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="w-full flex items-center justify-between gap-4 py-5 text-left text-[0.95rem] font-semibold text-[var(--white)] hover:text-[var(--orange)] transition"
                            :class="{ 'text-[var(--orange)]': open === {{ $index }} }"
                        >
                            <span>{{ $faq->question }}</span>
                            <i class="fa-solid fa-chevron-down text-[var(--orange)] transition-transform duration-300" 
                               :class="{ 'rotate-180': open === {{ $index }} }"></i>
                        </button>
                        
                        {{-- Answer with Alpine Collapse --}}
                        <div 
                            x-show="open === {{ $index }}"
                            x-collapse.duration.300ms
                            class="overflow-hidden"
                        >
                            <p class="pb-5 text-[0.9rem] text-[var(--white-dim)] leading-relaxed">
                                {{ $faq->answer }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
{{-- Alpine.js Core + Collapse Plugin (ORDRE CRITIQUE) --}}
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.5/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
@endpush