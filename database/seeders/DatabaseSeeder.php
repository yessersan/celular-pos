<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Nunca crear credenciales públicas o contraseñas predeterminadas.
        $email = env('POS_ADMIN_EMAIL');
        $password = env('POS_ADMIN_PASSWORD');

        if (is_string($email) && $email !== '' && is_string($password) && strlen($password) >= 12) {
            User::firstOrCreate(
                ['email' => $email],
                ['name' => env('POS_ADMIN_NAME', 'Administrador'), 'password' => Hash::make($password)]
            );
        }
    }
}
