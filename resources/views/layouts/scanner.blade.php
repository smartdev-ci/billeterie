<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner | {{ config('app.name') }}</title>
    @vite(['resources/css/staff.css', 'resources/js/staff.js'])
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-4xl p-6">
        <header class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-orange-600">Validation QR - Scanner</h1>
            <p class="text-sm text-gray-500">Connecté(e) en tant que {{ auth()->user()->name }}</p>
        </header>
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
