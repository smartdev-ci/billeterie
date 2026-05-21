@extends('layouts.staff')

@section('title', 'Dashboard Staff')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <a href="{{ route('staff.dashboard.live') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-medium">
            Voir le dashboard en direct
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Carte Scan QR -->
        <div class="bg-white p-6 rounded-xl border shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Scan QR</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Validez les billets des participants en scannant leurs QR codes.</p>
            <a href="{{ route('staff.qr.scan') }}" class="block text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                Accéder au scan
            </a>
        </div>

        <!-- Carte Commandes -->
        <div class="bg-white p-6 rounded-xl border shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Commandes</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Consultez et gérez les commandes des participants.</p>
            <a href="{{ route('staff.orders.index') }}" class="block text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                Voir les commandes
            </a>
        </div>

        <!-- Carte Analytics -->
        <div class="bg-white p-6 rounded-xl border shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Analytics</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Analysez les performances de l'événement en temps réel.</p>
            <a href="{{ route('staff.analytics.index') }}" class="block text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                Voir les analytics
            </a>
        </div>
    </div>
</div>
@endsection
