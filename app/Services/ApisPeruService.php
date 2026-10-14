<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ApisPeruService
{
    private function baseUrl(): string
    {
        return rtrim((string) config('services.apisperu.base_url', 'https://api.apisperu.pro'), '/');
    }

    private function token(): string
    {
        $clientId = (string) config('services.apisperu.client_id');
        $clientSecret = (string) config('services.apisperu.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('Configura APISPERU_CLIENT_ID y APISPERU_CLIENT_SECRET en el archivo .env.');
        }

        return Cache::remember('apisperu.account_token', now()->addMinutes(55), function () use ($clientId, $clientSecret) {
            $response = Http::baseUrl($this->baseUrl())
                ->acceptJson()
                ->asJson()
                ->timeout(10)
                ->post('/api/v1/auth/token', [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ]);

            if (! $response->successful() || ! $response->json('data.access_token')) {
                throw new RuntimeException('No fue posible autenticar con ApisPeru. Verifica las credenciales de cuenta y el plan activo.');
            }

            return (string) $response->json('data.access_token');
        });
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->withToken($this->token())
            ->timeout(10)
            ->retry(1, 200);
    }

    public function lookupDni(string $dni): array
    {
        if (! preg_match('/^[0-9]{8}$/', $dni)) {
            throw new RuntimeException('El DNI debe contener exactamente 8 dígitos.');
        }

        $response = $this->client()->get('/api/consultas/v1/dni/' . $dni);

        if ($response->status() === 404) {
            throw new RuntimeException('No se encontraron datos para el DNI ingresado.');
        }

        if (! $response->successful() || ! $response->json('success')) {
            throw new RuntimeException('ApisPeru no pudo consultar el DNI. Revisa las credenciales y el servicio.');
        }

        return [
            'document_type' => 'DNI',
            'document_number' => (string) $response->json('document_number', $dni),
            'name' => (string) $response->json('full_name', $response->json('display_name', '')),
            'address' => (string) $response->json('address', ''),
        ];
    }

    public function lookupRuc(string $ruc): array
    {
        if (! preg_match('/^[0-9]{11}$/', $ruc)) {
            throw new RuntimeException('El RUC debe contener exactamente 11 dígitos.');
        }

        $response = $this->client()->get('/api/consultas/v1/ruc/' . $ruc);

        if ($response->status() === 404) {
            throw new RuntimeException('No se encontraron datos para el RUC ingresado.');
        }

        if (! $response->successful() || ! $response->json('success')) {
            throw new RuntimeException('ApisPeru no pudo consultar el RUC. Revisa las credenciales y el servicio.');
        }

        return [
            'document_type' => 'RUC',
            'document_number' => (string) $response->json('document_number', $ruc),
            'name' => (string) $response->json('business_name', $response->json('display_name', '')),
            'address' => (string) $response->json('fiscal_address', $response->json('address', '')),
        ];
    }
}
