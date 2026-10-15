<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Céspedes Store - Sistema de Ventas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
<div x-data="{ sidebarOpen: false }" class="min-h-screen">
    <button type="button" x-cloak x-show="sidebarOpen" x-on:click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" aria-label="Cerrar menú"></button>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-950 text-slate-100 shadow-xl transition-transform duration-200 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-20 items-center gap-3 border-b border-white/10 px-5">
            @if(file_exists(public_path('image/logo.jpeg')))
                <img src="{{ asset('image/logo.jpeg') }}" alt="Logo Céspedes Store" class="h-11 w-11 rounded-xl bg-white object-contain p-1">
            @else
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 font-bold text-white">CS</div>
            @endif
            <div><p class="font-bold tracking-wide">CÉSPEDES STORE</p><p class="text-xs text-slate-400">Sistema de ventas</p></div>
            <button type="button" x-on:click="sidebarOpen = false" class="ml-auto rounded-lg p-2 text-slate-400 hover:bg-white/10 lg:hidden" aria-label="Cerrar menú">✕</button>
        </div>

        <div class="px-4 pt-5 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Menú principal</div>
        <nav class="flex-1 space-y-1 px-3">
            <a href="{{ route('products') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('products') ? 'bg-blue-600 font-semibold text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span class="w-6 text-center">📦</span><span>Productos</span>
            </a>
            <a href="{{ route('services') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('services') ? 'bg-blue-600 font-semibold text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span class="w-6 text-center">🛠️</span><span>Servicios</span>
            </a>
            <a href="{{ route('sales') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('sales') ? 'bg-blue-600 font-semibold text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span class="w-6 text-center">🛒</span><span>Nueva venta</span>
            </a>
            <a href="{{ route('reports') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('reports') ? 'bg-blue-600 font-semibold text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span class="w-6 text-center">📊</span><span>Reportes</span>
            </a>
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-xl bg-white/5 p-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div class="min-w-0"><p class="truncate text-sm font-semibold">{{ auth()->user()->name ?? 'Usuario' }}</p><p class="text-xs text-slate-400">Sesión activa</p></div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-red-500/15 hover:text-red-300"><span class="w-6 text-center">↪</span>Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 shadow-sm backdrop-blur sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" x-on:click="sidebarOpen = true" class="rounded-lg border border-slate-200 p-2 hover:bg-slate-100 lg:hidden" aria-label="Abrir menú">☰</button>
                <div><p class="font-semibold text-slate-800">@if(request()->routeIs('products')) Inventario de productos @elseif(request()->routeIs('services')) Gestión de servicios @elseif(request()->routeIs('sales')) Punto de venta @elseif(request()->routeIs('reports')) Reportes de ventas @else Sistema de ventas @endif</p><p class="hidden text-xs text-slate-500 sm:block">Administración del negocio</p></div>
            </div>
            <span class="hidden text-sm text-slate-500 sm:block">{{ now()->format('d/m/Y') }}</span>
        </header>
        <main class="mx-auto w-full max-w-[1600px] p-4 sm:p-6 lg:p-8">{{ $slot }}</main>
        <footer class="px-6 pb-5 text-center text-xs text-slate-400">Céspedes Store · Sistema de Ventas</footer>
    </div>
</div>
@livewireScripts
</body>
</html>
