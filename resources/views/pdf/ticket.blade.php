<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; text-align: center; padding: 20px; }
        .ticket { border: 2px dashed #333; padding: 20px; border-radius: 10px; }
        .qr { margin: 20px auto; }
        .info { margin: 10px 0; font-size: 14px; }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>Le Petit Poto </h2>
        <div class="info">Nom: {{ $order->customer_name }}</div>
        <div class="info">Email: {{ $order->customer_email }}</div>
        <div class="info">Quantité: {{ $order->quantity }}</div>
        <div class="info">Date: {{ $order->tickets->first()->event->event_date->format('d M Y H:i') }}</div>
        <div class="info">Lieu: {{ $order->tickets->first()->event->location }}</div>
        <div class="qr">
            {!! $order->tickets->first()->qr_code !!}
        </div>
        <p style="font-size:10px; color:#666;">UUID: {{ $order->tickets->first()->uuid }}</p>
    </div>
</body>
</html>