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
            throw ValidationException::withMessages(['customer_document' => 'El DNI debe contener exactamente 8 dígitos.']);
        }

        $token = config('services.apisperu.token');
        $url = config('services.apisperu.url');

        if (!$token || !$url) {
            throw new RuntimeException('ApisPeru no está configurado. Define APISPERU_TOKEN y APISPERU_URL en .env.');
        }

        try {
            $response = Http::acceptJson()->timeout(10)->get($url, [
                'numero' => $dni,
                'token' => $token,
            ]);
            $response->throw();
            $data = $response->json();

            if (!is_array($data) || ($data['success'] ?? true) === false) {
                throw new RuntimeException('ApisPeru no devolvió datos válidos para el DNI consultado.');
            }

            $person = $data['data'] ?? $data;
            $name = trim(implode(' ', array_filter([
                $person['nombres'] ?? $person['name'] ?? null,
                $person['apellidoPaterno'] ?? $person['apellido_paterno'] ?? null,
                $person['apellidoMaterno'] ?? $person['apellido_materno'] ?? null,
            ])));

            if ($name === '') {
                throw new RuntimeException('No se encontró una persona para el DNI indicado.');
            }

            return [
                'name' => $name,
                'document' => $dni,
                'address' => (string) ($person['direccion'] ?? $person['address'] ?? ''),
            ];
        } catch (\Illuminate\Http\Client\RequestException $exception) {
            Log::warning('Falló la consulta de DNI en ApisPeru.', ['status' => $exception->response?->status()]);
            throw new RuntimeException('No se pudo consultar ApisPeru. Verifica el token y vuelve a intentarlo.');
        }
    }
}
