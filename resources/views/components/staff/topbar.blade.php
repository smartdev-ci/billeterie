@props(['title' => ''])

<header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between">
    {{-- Left: Mobile Menu Toggle + Page Title --}}
    <div class="flex items-center gap-3">
        <button x-data @click="$dispatch('open-mobile-sidebar')" class="md:hidden p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h1 class="text-lg font-semibold text-gray-800 truncate">{{ $title }}</h1>
    </div>

    {{-- Right: User Info & Actions --}}
    <div class="flex items-center gap-3">
        {{-- Role Badge --}}
        <span class="hidden sm:inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full capitalize">
            {{ auth()->user()->role }}
        </span>

        {{-- User Avatar + Name --}}
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-600 flex items-center justify-center text-sm font-bold border border-orange-200">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Déconnexion">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
</header>