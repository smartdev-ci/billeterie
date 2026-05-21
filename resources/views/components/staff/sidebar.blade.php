<aside class="w-64 bg-white border-r border-gray-200 min-h-screen hidden md:flex flex-col">
    <div class="p-5 border-b">
        <span class="text-lg font-bold text-gray-800">LE PETIT POTO</span>
        <span class="block text-xs text-gray-500 mt-1">Espace Staff</span>
    </div>
    
    <nav class="flex-1 p-4 space-y-1">
        <a href="{{ route('staff.dashboard') }}" 
           class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('staff.dashboard') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            Dashboard
        </a>
        
        <a href="{{ route('staff.qr.scan') }}" 
           class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('staff.qr.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            Scan QR
        </a>
        
        <a href="{{ route('staff.orders.index') }}" 
           class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('staff.orders.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            Commandes
        </a>
        
        <a href="{{ route('staff.analytics.index') }}" 
           class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('staff.analytics.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            Analytics
        </a>
    </nav>

    <div class="p-4 border-t">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                Déconnexion
            </button>
        </form>
    </div>
</aside>