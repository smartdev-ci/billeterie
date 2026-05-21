@extends('layouts.admin')

@section('title', 'Commandes')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="font-semibold">Liste des commandes</h2>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..." class="px-3 py-1.5 border rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <select name="status" class="px-3 py-1.5 border rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">Tous</option>
                <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Payées</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Échouées</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800 transition">Filtrer</button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3">UUID</th>
                    <th class="px-6 py-3">Client</th>
                    <th class="px-6 py-3">Statut</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Paiement</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $order->uuid }}</td>
                    <td class="px-6 py-3">
                        <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                        <div class="text-gray-500 text-xs">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-6 py-3">
                        @php $colors = ['pending' => 'yellow', 'completed' => 'green', 'failed' => 'red', 'refunded' => 'gray']; @endphp
                        <span class="px-2 py-1 bg-{{ $colors[$order->payment_status] }}-100 text-{{ $colors[$order->payment_status] }}-800 rounded-full text-xs font-medium">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 font-semibold text-gray-900">{{ number_format($order->total_amount) }} FCFA</td>
                    <td class="px-6 py-3 text-gray-600">{{ ucfirst($order->mobile_provider ?? '-') }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t">
        {{ $orders->links() }}
    </div>
</div>
@endsection