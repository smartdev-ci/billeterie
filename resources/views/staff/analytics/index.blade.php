<x-layouts.staff title="Analytics & Rapports">
    <div class="space-y-6" x-data="analyticsChart(@json($chartData))">
        <!-- Header & Export -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tableau de bord analytique</h1>
                <p class="text-sm text-gray-500">Données mises a jour automatiquement.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('staff.analytics.export.csv') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Exporter CSV
                </a>
                <a href="{{ route('staff.analytics.export.pdf') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                    Exporter PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Billets vendus</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['tickets_sold']) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Revenus generes</p>
                <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($stats['revenue'], 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Billets restants</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($stats['tickets_remaining']) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Commandes validees</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ventes journalieres (14 jours)</h2>
            <div class="relative h-64 w-full">
                <canvas x-ref="chart"></canvas>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Commandes recentes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">Client</th>
                                <th class="px-6 py-3 font-medium">Montant</th>
                                <th class="px-6 py-3 font-medium">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                                    <div class="text-gray-500 text-xs">{{ $order->customer_email }}</div>
                                </td>
                                <td class="px-6 py-3 font-medium">{{ number_format($order->total_amount) }} FCFA</td>
                                <td class="px-6 py-3 text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Validations -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Dernieres validations</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">Ticket</th>
                                <th class="px-6 py-3 font-medium">Validateur</th>
                                <th class="px-6 py-3 font-medium">Heure</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentValidations as $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-900">...{{ substr($ticket->uuid, -6) }}</div>
                                    <div class="text-gray-500 text-xs">{{ $ticket->customer_email }}</div>
                                </td>
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->validator?->name ?? 'Systeme' }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $ticket->used_at->format('H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endpush

    @push('scripts')
        <script>
            function analyticsChart(data) {
                return {
                    init() {
                        new Chart(this.$refs.chart, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Revenus (FCFA)',
                                    data: data.revenues,
                                    borderColor: '#f97316',
                                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                                    fill: true,
                                    tension: 0.3
                                }, {
                                    label: 'Billets vendus',
                                    data: data.sales,
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'transparent',
                                    borderDash: [4, 4],
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'top' }
                                },
                                scales: {
                                    y: { beginAtZero: true }
                                }
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
</x-layouts.staff>