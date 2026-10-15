<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ApisPeruService
{
    public function lookupDni(string $dni): array
    {
        if (!preg_match('/^\d{8}$/', $dni)) {
            throw ValidationException::withMessages([
                'customer_document' => 'El DNI debe contener exactamente 8 dígitos.',
            ]);
        }

        $token = config('services.apisperu.token');
        $url = rtrim(config('services.apisperu.url', ''), '/');

        if (!$token || !$url) {
            throw new RuntimeException(
                'ApisPeru no está configurado. Verifica APISPERU_TOKEN y APISPERU_URL.'
            );
        }

        try {
            // El DNI se envía en la URL y el token en la cabecera.
            $response = Http::acceptJson()
                ->withToken($token)
                ->timeout(15)
                ->get($url . '/' . $dni);

            if ($response->failed()) {
                Log::warning('Error HTTP al consultar DNI en ApisPeru.', [
                    'status' => $response->status(),
                ]);

                if (in_array($response->status(), [401, 403])) {
                    throw new RuntimeException(
                        'ApisPeru rechazó las credenciales. Verifica el token y el servicio contratado.'
                    );
                }

                throw new RuntimeException(
                    'ApisPeru respondió con un error HTTP: ' . $response->status()
                );
            }

            $data = $response->json();

            if (!is_array($data) || ($data['success'] ?? true) === false) {
                throw new RuntimeException(
                    'ApisPeru no devolvió datos válidos para el DNI consultado.'
                );
            }

            $person = $data['data'] ?? $data;

            $name = trim(implode(' ', array_filter([
                $person['nombres'] ?? $person['name'] ?? null,
                $person['apellidoPaterno'] ?? $person['apellido_paterno'] ?? null,
                $person['apellidoMaterno'] ?? $person['apellido_materno'] ?? null,
            ])));

            if ($name === '') {
                throw new RuntimeException(
                    'No se encontraron datos de nombre para el DNI consultado.'
                );
            }

            return [
                'name' => $name,
                'document' => $dni,
                'address' => (string) (
                    $person['direccion'] ?? $person['address'] ?? ''
                ),
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            Log::warning('No se pudo conectar con ApisPeru.', [
                'error' => $exception->getMessage(),
            ]);

            throw new RuntimeException(
                'No se pudo conectar con ApisPeru. Inténtalo nuevamente.'
            );
        }
    }
}
