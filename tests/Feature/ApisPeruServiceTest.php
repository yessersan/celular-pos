<?php

namespace Tests\Feature;

use App\Services\ApisPeruService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApisPeruServiceTest extends TestCase
{
    public function test_dni_lookup_maps_person_fields(): void
    {
        config([
            'services.apisperu.url' => 'https://api.example.test/dni',
            'services.apisperu.token' => 'test-token',
        ]);

        Http::fake([
            'api.example.test/*' => Http::response([
                'success' => true,
                'data' => [
                    'nombres' => 'ANA MARIA',
                    'apellidoPaterno' => 'PEREZ',
                    'apellidoMaterno' => 'LOPEZ',
                    'direccion' => 'Huanuco',
                ],
            ], 200),
        ]);

        $person = app(ApisPeruService::class)->lookupDni('12345678');

        $this->assertSame('ANA MARIA PEREZ LOPEZ', $person['name']);
        $this->assertSame('12345678', $person['document']);
        $this->assertSame('Huanuco', $person['address']);
        Http::assertSent(fn ($request) => $request['numero'] === '12345678' && $request['token'] === 'test-token');
    }

    public function test_invalid_dni_is_rejected_before_http_call(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(ApisPeruService::class)->lookupDni('1234');
    }
}
