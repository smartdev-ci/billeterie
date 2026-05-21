<x-mail::message>
# Merci {{ $order->customer_name }}

Votre paiement de **{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA** a bien été reçu.

Vos billets sont joints à cet email. Présentez le QR Code à l'entrée.

<x-mail::button :url="route('event.show')">
Voir les détails de l'événement
</x-mail::button>

À très vite,<br>
{{ config('app.name') }}
</x-mail::message>