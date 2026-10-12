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
            <div class="flex flex-wrap justify-between min-h-20 items-center gap-3 py-3">
                <div class="flex items-center gap-3">
                    @if(file_exists(public_path('image/logo.jpeg')))
                        <img src="{{ asset('image/logo.jpeg') }}" alt="Logo" class="h-12 w-12 rounded-full bg-white p-1 shadow">
                    @endif
                    <div><div class="font-bold text-white text-lg leading-tight">CÉSPEDES STORE</div><div class="text-blue-100 text-xs">Sistema de Ventas</div></div>
                </div>
                <div class="flex flex-wrap items-center gap-1">
                    <a href="{{ route('products') }}" class="px-3 py-2 rounded-lg text-white hover:bg-blue-700 {{ request()->routeIs('products') ? 'bg-blue-900 font-semibold' : '' }}">📦 Inventario</a>
                    <a href="{{ route('services') }}" class="px-3 py-2 rounded-lg text-white hover:bg-blue-700 {{ request()->routeIs('services') ? 'bg-blue-900 font-semibold' : '' }}">🔧 Servicios</a>
                    <a href="{{ route('sales') }}" class="px-3 py-2 rounded-lg text-white hover:bg-blue-700 {{ request()->routeIs('sales') ? 'bg-blue-900 font-semibold' : '' }}">🛒 Nueva Venta</a>
                    <a href="{{ route('reports') }}" class="px-3 py-2 rounded-lg text-white hover:bg-blue-700 {{ request()->routeIs('reports') ? 'bg-blue-900 font-semibold' : '' }}">📊 Reportes</a>
                    <span class="text-white text-sm px-2">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="px-3 py-2 rounded-lg bg-blue-900 text-white hover:bg-blue-950">Cerrar sesión</button></form>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 pb-12">{{ $slot }}</main>
    @livewireScripts
</body>
</html>
