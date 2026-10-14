<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Céspedes Store - Sistema de Ventas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="mb-6 bg-gradient-to-r from-blue-800 to-blue-600 shadow-lg">
        <div class="mx-auto flex min-h-20 max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <div class="flex items-center gap-3">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-12 rounded-full bg-white p-1 shadow">
                @endif
                <div><div class="text-lg font-bold leading-tight text-white">CÉSPEDES STORE</div><div class="text-xs text-blue-100">Sistema de Ventas</div></div>
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <a href="{{ route('products') }}" class="rounded-lg px-3 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('products') ? 'bg-blue-900 font-semibold' : '' }}">Inventario</a>
                <a href="{{ route('services') }}" class="rounded-lg px-3 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('services') ? 'bg-blue-900 font-semibold' : '' }}">Servicios</a>
                <a href="{{ route('sales') }}" class="rounded-lg px-3 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('sales') ? 'bg-blue-900 font-semibold' : '' }}">Nueva venta</a>
                <a href="{{ route('reports') }}" class="rounded-lg px-3 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('reports') ? 'bg-blue-900 font-semibold' : '' }}">Reportes</a>
                <span class="px-2 text-sm text-white">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="rounded-lg bg-white/15 px-3 py-2 text-white hover:bg-white/25">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 pb-12">{{ $slot }}</main>
    @livewireScripts
</body>
</html>
