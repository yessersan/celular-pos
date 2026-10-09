<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Céspedes Store - Sistema de Ventas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-gradient-to-r from-blue-800 to-blue-600 shadow-lg mb-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Logo"
                             class="h-14 w-14 rounded-full bg-white p-1 shadow">
                    @endif
                    <div>
                        <div class="font-bold text-white text-lg leading-tight">CÉSPEDES STORE</div>
                        <div class="text-blue-100 text-xs">Sistema de Ventas</div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('products') }}"
                       class="px-4 py-2 rounded-lg text-white hover:bg-blue-700 transition
                              {{ request()->routeIs('products') ? 'bg-blue-900 font-semibold' : '' }}">
                        📦 Inventario
                    </a>
                    <a href="{{ route('services') }}"
                       class="px-4 py-2 rounded-lg text-white hover:bg-blue-700 transition
                              {{ request()->routeIs('services') ? 'bg-blue-900 font-semibold' : '' }}">
                        🔧 Servicios
                    </a>
                    <a href="{{ route('sales') }}"
                       class="px-4 py-2 rounded-lg text-white hover:bg-blue-700 transition
                              {{ request()->routeIs('sales') ? 'bg-blue-900 font-semibold' : '' }}">
                        🛒 Nueva Venta
                    </a>
                    <a href="{{ route('reports') }}"
                       class="px-4 py-2 rounded-lg text-white hover:bg-blue-700 transition
                              {{ request()->routeIs('reports') ? 'bg-blue-900 font-semibold' : '' }}">
                        📊 Reportes
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 pb-12">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>