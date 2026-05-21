@props(['items'])

<section class="py-20 bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">ILS Y ETAIENT. ILS EN PARLENT</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($items as $testimonial)
                <div class="bg-black/50 border border-white/10 rounded-2xl p-6">
                    <div class="flex items-start mb-4">
                        <div class="flex-1">
                            <p class="text-white/80 mb-4 leading-relaxed">"{{ $testimonial->quote }}"</p>
                            <div class="flex items-center">
                                <div>
                                    <div class="font-semibold text-white">{{ $testimonial->name }}</div>
                                    <div class="text-sm text-white/50 flex items-center">
                                        @include("components.public.social-icons.{$testimonial->platform}")
                                        <span class="ml-1">@{{ $testimonial->handle }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>