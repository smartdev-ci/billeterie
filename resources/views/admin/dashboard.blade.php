@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Billets vendus</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['tickets_sold']) }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Billets restants</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($stats['tickets_remaining']) }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Revenus totaux</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($stats['revenue'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Commandes validées</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chart --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="font-semibold text-gray-900 mb-4">Revenus (7 derniers jours)</h3>
            <div class="relative h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-900">Dernières commandes</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($recentOrders as $order)
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->customer_email }}</p>
                        </div>
                        <span class="text-sm font-semibold text-green-700">+{{ number_format($order->total_amount) }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                </li>
                @endforeach
            </ul>
            <div class="px-6 py-3 bg-gray-50 border-t">
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Voir toutes →</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Revenus (FCFA)',
                    data: @json($revenues),
                    borderColor: '#ea580c',
                    backgroundColor: 'rgba(234, 88, 12, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    </script>
    @endpush
@endsection