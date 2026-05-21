<!DOCTYPE html>
<html lang="fr" class="dark scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Le Petit Poto - La soiree la plus electrique d'Abidjan.">
    <title>{{ config('app.name', 'Le Petit Poto') }}</title>

    {{-- Fonts & Icons (FontAwesome inclus) --}}
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com"> --}}
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" --}}
        {{-- rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> --}}

    {{-- Fonts Premium --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- FontAwesome (icônes uniquement) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Vite Assets --}}
    @vite(['resources/css/public.css', 'resources/js/public.js'])

    {{-- Alpine.js Collapse Plugin --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.5/dist/cdn.min.js"></script>

    @stack('head')
</head>

<body class="bg-black text-white antialiased selection:bg-orange-500/30">

    {{-- Correction ici : @yield au lieu de {{ $slot }} --}}
    @yield('content')

    @stack('scripts')
</body>

</html>