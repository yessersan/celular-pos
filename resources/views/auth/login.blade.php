<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Céspedes Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <main class="w-full max-w-md rounded-xl bg-white p-8 shadow">
        <h1 class="text-2xl font-bold text-gray-800">CÉSPEDES STORE</h1>
        <p class="mt-1 mb-6 text-gray-500">Inicia sesión para acceder al punto de venta.</p>

        @if ($errors->any())
            <div role="alert" class="mb-4 rounded bg-red-100 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="w-full rounded border px-3 py-2">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="w-full rounded border px-3 py-2">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" value="1"> Mantener sesión iniciada
            </label>
            <button type="submit" class="w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">
                Iniciar sesión
            </button>
        </form>
    </main>
</body>
</html>
