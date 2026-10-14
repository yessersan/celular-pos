<?php

namespace Tests\Feature;

use App\Services\ApisPeruService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class ApisPeruServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.apisperu.base_url' => 'https://dniruc.apisperu.com/api/v1',
            'services.apisperu.token' => 'test-token',
        ]);
    }

    public function test_it_looks_up_dni_using_the_documented_endpoint(): void
    {
        Http::fake([
            'dniruc.apisperu.com/api/v1/dni/12345678*' => Http::response([
                'dni' => '12345678',
                'nombres' => 'JUAN',
                'apellidoPaterno' => 'PEREZ',
                'apellidoMaterno' => 'GOMEZ',
            ]),
        ]);

        $result = app(ApisPeruService::class)->lookupDni('12345678');

        $this->assertSame('DNI', $result['document_type']);
        $this->assertSame('JUAN PEREZ GOMEZ', $result['name']);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/dni/12345678') && $request['token'] === 'test-token');
    }

    public function test_it_rejects_invalid_dni_before_calling_api(): void
    {
        Http::fake();
        $this->expectException(RuntimeException::class);
        app(ApisPeruService::class)->lookupDni('1234');
    }

    public function test_it_reports_missing_token_without_calling_api(): void
    {
        config(['services.apisperu.token' => '']);
        Http::fake();
        $this->expectException(RuntimeException::class);
        app(ApisPeruService::class)->lookupDni('12345678');
    }
}
