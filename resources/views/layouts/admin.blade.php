<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Le Petit Poto</title>
    @vite(['resources/css/staff.css', 'resources/js/staff.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex">
    <aside class="w-64 bg-white border-r border-gray-200 min-h-screen hidden md:flex flex-col">
        <div class="p-5 border-b">
            <span class="text-lg font-bold text-gray-800">LE PETIT POTO</span>
            <span class="block text-xs text-gray-500 mt-1">Administration</span>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Dashboard</a>
            <a href="{{ route('admin.orders.index') }}"
                class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Commandes</a>
            <a href="{{ route('admin.event.config') }}"
                class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.event.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Configuration</a>
        </nav>
        <div class="p-4 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Déconnexion</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-h-screen">
        <header class="bg-white border-b px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">{{ $title ?? 'Administration' }}</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                <span
                    class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </header>
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}</div> @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}
            </div> @endif
            @yield('content')
        </div>
    </main>
</body>

</html>