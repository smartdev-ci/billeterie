@extends('layouts.staff')

@section('title', 'Dashboard Live')

@section('content')
<div x-data="liveDashboard()" x-init="startPolling()" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Billets vendus</p>
            <p class="mt-2 text-3xl font-bold" x-text="metrics.sold">0</p>
        </div>
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Taux de remplissage</p>
            <p class="mt-2 text-3xl font-bold text-orange-600">
                <span x-text="metrics.fill_rate">0</span>%
            </p>
        </div>
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Revenus (FCFA)</p>
            <p class="mt-2 text-3xl font-bold text-green-600" x-text="formatMoney(metrics.revenue)">0</p>
        </div>
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Validations (1h)</p>
            <p class="mt-2 text-3xl font-bold text-blue-600" x-text="metrics.recent_validations">0</p>
        </div>
    </div>

    <p class="text-xs text-gray-400" x-text="'Dernière mise à jour : ' + lastUpdate"></p>
</div>

@push('scripts')
<script>
function liveDashboard() {
    return {
        metrics: @json($metrics),
        lastUpdate: 'Initialisation...',
        interval: null,

        startPolling() {
            this.updateTime();
            this.interval = setInterval(() => this.fetchStats(), 30000); // 30s
        },

        async fetchStats() {
            try {
                const res = await fetch('{{ route("staff.dashboard.stats") }}');
                this.metrics = await res.json();
                this.updateTime();
            } catch (e) {
                console.warn('Live stats polling failed', e);
            }
        },

        updateTime() {
            const now = new Date();
            this.lastUpdate = now.toLocaleTimeString('fr-FR');
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount || 0);
        }
    }
}
</script>
@endpush
@endsection