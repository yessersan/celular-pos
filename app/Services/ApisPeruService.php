<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ApisPeruService
{
    private function token(): string
    {
        $token = (string) config('services.apisperu.token');

        if ($token === '') {
            throw new RuntimeException('Configura APISPERU_TOKEN en el archivo .env.');
        }

        return $token;
    }

    private function lookup(string $type, string $number): array
    {
        $rules = ['dni' => 8, 'ruc' => 11];

        if (! isset($rules[$type]) || ! preg_match('/^[0-9]{' . $rules[$type] . '}$/', $number)) {
            throw new RuntimeException($type === 'dni'
                ? 'El DNI debe contener exactamente 8 dígitos.'
                : 'El RUC debe contener exactamente 11 dígitos.');
        }

        $response = Http::baseUrl(rtrim((string) config('services.apisperu.base_url', 'https://dniruc.apisperu.com/api/v1'), '/'))
            ->acceptJson()
            ->timeout(10)
            ->retry(1, 200)
            ->get('/' . $type . '/' . $number, ['token' => $this->token()]);

        if ($response->status() === 404) {
            throw new RuntimeException('No se encontraron datos para el documento ingresado.');
        }

        if (! $response->successful()) {
            throw new RuntimeException('ApisPeru no pudo consultar el documento. Revisa el token y el servicio.');
        }

        $data = $response->json();

        if (! is_array($data) || empty($data)) {
            throw new RuntimeException('ApisPeru devolvió una respuesta vacía o no válida.');
        }

        if ($type === 'dni') {
            $name = trim(implode(' ', array_filter([
                $data['nombres'] ?? '',
                $data['apellidoPaterno'] ?? '',
                $data['apellidoMaterno'] ?? '',
            ])));

            return [
                'document_type' => 'DNI',
                'document_number' => (string) ($data['dni'] ?? $number),
                'name' => $name,
                'address' => '',
            ];
        }

        return [
            'document_type' => 'RUC',
            'document_number' => (string) ($data['ruc'] ?? $number),
            'name' => (string) ($data['razonSocial'] ?? $data['nombreComercial'] ?? ''),
            'address' => (string) ($data['direccion'] ?? ''),
        ];
    }

    public function lookupDni(string $dni): array
    {
        return $this->lookup('dni', $dni);
    }

    public function lookupRuc(string $ruc): array
    {
        return $this->lookup('ruc', $ruc);
    }
}
