<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | {{ config('app.name') }}</title>
    @vite(['resources/css/staff.css', 'resources/js/staff.js'])
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex">
    <x-staff.sidebar />
    
    <div class="flex-1 flex flex-col min-h-screen">
        <x-staff.topbar />
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>