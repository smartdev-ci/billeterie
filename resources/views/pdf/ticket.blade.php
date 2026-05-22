<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background: #0B0B0B;
            color: #F5F5F5;
            width: 800px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #FF7A00, #cc4e00);
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 6px;
            color: #fff;
            text-transform: uppercase;
        }
        .edition-badge {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.4);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #fff;
        }

        /* Body */
        .ticket-body {
            background: #111111;
            padding: 40px;
        }

        /* Validity banner */
        .valid-banner {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.4);
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .valid-dot {
            width: 10px;
            height: 10px;
            background: #22c55e;
            border-radius: 50%;
        }
        .valid-text {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #22c55e;
        }

        /* Content grid */
        .content-grid {
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }
        .info-section {
            flex: 1;
        }
        .qr-section {
            text-align: center;
            flex-shrink: 0;
        }

        /* Info rows */
        .info-label {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #FF7A00;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 18px;
            font-weight: bold;
            color: #F5F5F5;
            margin-bottom: 24px;
        }
        .info-value.large {
            font-size: 22px;
        }

        /* Event details */
        .event-row {
            display: flex;
            gap: 32px;
            margin-bottom: 24px;
        }
        .event-item { flex: 1; }

        /* QR Code */
        .qr-container {
            background: #fff;
            padding: 16px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .qr-container img { width: 160px; height: 160px; display: block; }
        .qr-hint {
            font-size: 11px;
            color: rgba(245, 245, 245, 0.5);
            text-align: center;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid rgba(245,245,245,0.1);
            margin: 28px 0;
        }

        /* UUID */
        .uuid-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .uuid-label {
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(245, 245, 245, 0.4);
        }
        .uuid-value {
            font-size: 11px;
            font-family: monospace;
            color: rgba(245, 245, 245, 0.6);
        }

        /* Footer */
        .footer {
            background: #0B0B0B;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255,122,0,0.3);
        }
        .footer-text {
            font-size: 11px;
            color: rgba(245, 245, 245, 0.4);
        }
        .footer-brand {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #FF7A00;
            text-transform: uppercase;
        }

        /* Perforations */
        .perforation {
            background: #0B0B0B;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
        }
        .perf-dot {
            width: 20px;
            height: 20px;
            background: #0B0B0B;
            border-radius: 50%;
            border: 1px dashed rgba(255,122,0,0.3);
        }
    </style>
</head>
<body>

<div class="header">
    <div class="brand">Le Petit Poto</div>
    <div class="edition-badge">Billet Officiel</div>
</div>

<div class="ticket-body">
    <div class="valid-banner">
        <div class="valid-dot"></div>
        <div class="valid-text">Billet Valide · Accès Garanti</div>
    </div>

    <div class="content-grid">
        <div class="info-section">
            <div class="info-label">Titulaire</div>
            <div class="info-value large">{{ $order->customer_name }}</div>

            <div class="event-row">
                <div class="event-item">
                    <div class="info-label">Date</div>
                    <div class="info-value">{{ $order->tickets->first()->event->event_date->format('d M Y') }}</div>
                </div>
                <div class="event-item">
                    <div class="info-label">Heure</div>
                    <div class="info-value">{{ $order->tickets->first()->event->event_date->format('H:i') }}</div>
                </div>
            </div>

            <div class="info-label">Lieu</div>
            <div class="info-value">{{ $order->tickets->first()->event->location }}</div>

            <div class="info-label">Nombre de billets</div>
            <div class="info-value">{{ $order->quantity }} billet{{ $order->quantity > 1 ? 's' : '' }}</div>
        </div>

        <div class="qr-section">
            <div class="qr-container">
                @php
                    $ticket = $order->tickets->first();
                    // Extract base64 SVG or use QR code data
                    $qrData = $ticket->qr_code;
                @endphp
                @if (str_starts_with($qrData, 'data:image/svg+xml;base64,'))
                    <img src="{{ $qrData }}" alt="QR Code">
                @else
                    <img src="{{ $qrData }}" alt="QR Code" style="width:160px;height:160px;">
                @endif
            </div>
            <div class="qr-hint">Scanner à l'entrée</div>
        </div>
    </div>

    <hr class="divider">

    <div class="uuid-row">
        <div class="uuid-label">Référence Ticket</div>
        <div class="uuid-value">{{ $order->tickets->first()->uuid }}</div>
    </div>
</div>

<div class="footer">
    <div class="footer-text">Non remboursable · Non transférable · Usage unique</div>
    <div class="footer-brand">Le Petit Poto</div>
</div>

</body>
</html>