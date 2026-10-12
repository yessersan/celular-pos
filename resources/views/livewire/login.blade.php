<div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
    <h1 class="text-2xl font-bold text-center mb-2">CÉSPEDES STORE</h1>
    <p class="text-gray-500 text-center mb-6">Inicia sesión para acceder al punto de venta</p>
    <form wire:submit="authenticate" class="space-y-4">
        <div><label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label><input id="email" type="email" wire:model="email" autocomplete="username" required class="w-full border rounded-lg p-3">@error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror</div>
        <div><label for="password" class="block text-sm font-medium mb-1">Contraseña</label><input id="password" type="password" wire:model="password" autocomplete="current-password" required class="w-full border rounded-lg p-3">@error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror</div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="remember"> Mantener sesión iniciada</label>
        <button type="submit" wire:loading.attr="disabled" class="w-full bg-blue-700 hover:bg-blue-800 text-white rounded-lg p-3 font-semibold">Ingresar</button>
    </form>
</div>
