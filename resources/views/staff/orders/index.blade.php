@extends('layouts.staff')

@section('title', 'Gestion des Commandes')

@section('content')
<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="p-6 border-b flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <form method="GET" class="flex flex-wrap gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom, email, UUID..." class="px-3 py-2 border rounded-lg text-sm">
            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Tous les statuts</option>
                <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Payees</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Echouees</option>
            </select>
            <select name="provider" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Tous les providers</option>
                <option value="orange" {{ ($provider ?? '') === 'orange' ? 'selected' : '' }}>Orange Money</option>
                <option value="mtn" {{ ($provider ?? '') === 'mtn' ? 'selected' : '' }}>MTN Money</option>
                <option value="moov" {{ ($provider ?? '') === 'moov' ? 'selected' : '' }}>Moov Money</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800">Filtrer</button>
        </form>
        <a href="{{ route('staff.orders.export') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Exporter CSV</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3">UUID</th>
                    <th class="px-6 py-3">Client</th>
                    <th class="px-6 py-3">Statut</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Provider</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs">{{ $order->uuid }}</td>
                    <td class="px-6 py-3">
                        <div class="font-medium">{{ $order->customer_name }}</div>
                        <div class="text-gray-500 text-xs">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-6 py-3">
                        @php $colors = ['pending' => 'yellow', 'completed' => 'green', 'failed' => 'red', 'refunded' => 'gray']; @endphp
                        <span class="px-2 py-1 bg-{{ $colors[$order->payment_status] }}-100 text-{{ $colors[$order->payment_status] }}-800 rounded-full text-xs">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                    <td class="px-6 py-3 font-semibold">{{ number_format($order->total_amount) }} FCFA</td>
                    <td class="px-6 py-3">{{ ucfirst($order->mobile_provider ?? '-') }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $orders->links() }}</div>
</div>
@endsection