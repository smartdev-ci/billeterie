@props(['target'])

<div 
    x-data="countdown(@js($target))"
    class="flex items-start gap-1 sm:gap-2"
>
    {{-- Days --}}
    <div class="flex flex-col items-center gap-1 sm:gap-2 min-w-[64px] sm:min-w-[72px] bg-white/6 backdrop-blur-sm border border-white/10 rounded-xl sm:rounded-2xl px-2 sm:px-3 py-3 sm:py-4">
        <span x-text="days" class="font-display text-3xl sm:text-4xl md:text-5xl tracking-wide leading-none text-orange-400"></span>
        <span class="text-[10px] sm:text-xs font-semibold tracking-widest uppercase text-white/60">Jours</span>
    </div>

    <span class="font-display text-2xl sm:text-3xl md:text-4xl text-orange-400 pt-2 sm:pt-3 leading-none opacity-50">:</span>

    {{-- Hours --}}
    <div class="flex flex-col items-center gap-1 sm:gap-2 min-w-[64px] sm:min-w-[72px] bg-white/6 backdrop-blur-sm border border-white/10 rounded-xl sm:rounded-2xl px-2 sm:px-3 py-3 sm:py-4">
        <span x-text="hours" class="font-display text-3xl sm:text-4xl md:text-5xl tracking-wide leading-none text-orange-400"></span>
        <span class="text-[10px] sm:text-xs font-semibold tracking-widest uppercase text-white/60">Heures</span>
    </div>

    <span class="font-display text-2xl sm:text-3xl md:text-4xl text-orange-400 pt-2 sm:pt-3 leading-none opacity-50">:</span>

    {{-- Minutes --}}
    <div class="flex flex-col items-center gap-1 sm:gap-2 min-w-[64px] sm:min-w-[72px] bg-white/6 backdrop-blur-sm border border-white/10 rounded-xl sm:rounded-2xl px-2 sm:px-3 py-3 sm:py-4">
        <span x-text="minutes" class="font-display text-3xl sm:text-4xl md:text-5xl tracking-wide leading-none text-orange-400"></span>
        <span class="text-[10px] sm:text-xs font-semibold tracking-widest uppercase text-white/60">Min</span>
    </div>

    <span class="font-display text-2xl sm:text-3xl md:text-4xl text-orange-400 pt-2 sm:pt-3 leading-none opacity-50">:</span>

    {{-- Seconds --}}
    <div class="flex flex-col items-center gap-1 sm:gap-2 min-w-[64px] sm:min-w-[72px] bg-white/6 backdrop-blur-sm border border-white/10 rounded-xl sm:rounded-2xl px-2 sm:px-3 py-3 sm:py-4">
        <span x-text="seconds" class="font-display text-3xl sm:text-4xl md:text-5xl tracking-wide leading-none text-orange-400"></span>
        <span class="text-[10px] sm:text-xs font-semibold tracking-widest uppercase text-white/60">Sec</span>
    </div>
</div>

@push('scripts')
<script>
function countdown(targetDate) {
    return {
        days: '00',
        hours: '00',
        minutes: '00',
        seconds: '00',
        
        init() {
            const update = () => {
                const now = Date.now();
                const target = new Date(targetDate).getTime();
                const diff = target - now;

                if (diff <= 0) {
                    this.days = this.hours = this.minutes = this.seconds = '00';
                    return;
                }

                this.days = String(Math.floor(diff / 86400000)).padStart(2, '0');
                this.hours = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
                this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            };

            update();
            const timer = setInterval(update, 1000);

            // Cleanup on Alpine component destroy
            this.$watch('$el', () => () => clearInterval(timer));
        }
    }
}
</script>
@endpush