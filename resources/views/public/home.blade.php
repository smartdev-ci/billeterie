@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <main class="relative">
        {{-- Hero Section --}}
        <x-public.hero 
            :event="$event"
            :countdown-end="$event->event_date"
        />

        {{-- Events Grid --}}
        <x-public.events-grid :events="$events" />

        {{-- Ticketing Section --}}
        <x-public.ticketing-section 
            :price="5000"
            :early-bird-remaining="23"
            :payment-methods="['orange-money', 'mtn-money', 'wave', 'card']"
        />

        {{-- Gallery Preview --}}
        <x-public.gallery-preview />

        {{-- Testimonials --}}
        <x-public.testimonials :items="$testimonials" />

        {{-- Stats Bar --}}
        <x-public.stats-bar 
            :rating="4.9"
            :reviews="1200"
            :return-rate="98"
            :sold-editions="5"
        />

        {{-- FAQ Section --}}
        <x-public.faq-section :questions="$faqItems" />
    </main>

    <x-public.footer />
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
    <script>
        function countdown(targetDate) {
            return {
                days: '00',
                hours: '00',
                minutes: '00',
                seconds: '00',
                init() {
                    const update = () => {
                        const now = new Date().getTime();
                        const distance = new Date(targetDate).getTime() - now;
                        
                        if (distance <= 0) {
                            this.days = this.hours = this.minutes = this.seconds = '00';
                            return;
                        }
                        
                        this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                        this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                        this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                        this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                    };
                    
                    update();
                    setInterval(update, 1000);
                }
            }
        }
    </script>
@endpush