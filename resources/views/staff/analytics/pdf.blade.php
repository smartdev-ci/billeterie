<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; color: #111827; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .stat-box { width: 23%; text-align: center; padding: 15px; background: #f9fafb; border-radius: 6px; }
        .stat-box h3 { margin: 0 0 5px 0; font-size: 14px; color: #6b7280; }
        .stat-box p { margin: 0; font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Le Petit Poto - Rapport Analytique</h1>
        <p>Genere le {{ now()->format('d/m/Y a H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>Billets Vendus</h3>
            <p>{{ number_format($stats['tickets_sold']) }}</p>
        </div>
        <div class="stat-box">
            <h3>Revenus Totaux</h3>
            <p>{{ number_format($stats['revenue'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="stat-box">
            <h3>Billets Restants</h3>
            <p>{{ number_format($stats['tickets_remaining']) }}</p>
        </div>
        <div class="stat-box">
            <h3>Commandes</h3>
            <p>{{ number_format($stats['total_orders']) }}</p>
        </div>
    </div>

    <h2>Dernieres commandes</h2>
    <table>
        <thead>
            <tr>
                <th>UUID</th>
                <th>Client</th>
                <th>Email</th>
                <th>Montant</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->uuid }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->customer_email }}</td>
                <td>{{ number_format($order->total_amount) }} FCFA</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>