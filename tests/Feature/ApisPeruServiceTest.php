<?php

namespace Tests\Feature;

use App\Services\ApisPeruService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class ApisPeruServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.apisperu.base_url' => 'https://api.apisperu.pro',
            'services.apisperu.client_id' => 'test-client',
            'services.apisperu.client_secret' => 'test-secret',
        ]);
        Cache::flush();
    }

    public function test_it_looks_up_a_dni_using_apisperu(): void
    {
        Http::fake([
            'api.apisperu.pro/api/v1/auth/token' => Http::response([
                'success' => true,
                'data' => ['access_token' => 'test-token'],
            ]),
            'api.apisperu.pro/api/consultas/v1/dni/12345678' => Http::response([
                'success' => true,
                'document_number' => '12345678',
                'full_name' => 'JUAN PEREZ',
            ]),
        ]);

        $result = app(ApisPeruService::class)->lookupDni('12345678');

        $this->assertSame('DNI', $result['document_type']);
        $this->assertSame('JUAN PEREZ', $result['name']);
    }

    public function test_it_rejects_invalid_dni_before_calling_the_api(): void
    {
        $this->expectException(RuntimeException::class);

        app(ApisPeruService::class)->lookupDni('1234');
    }

    public function test_it_reports_missing_credentials_without_calling_the_api(): void
    {
        config(['services.apisperu.client_id' => '', 'services.apisperu.client_secret' => '']);

        $this->expectException(RuntimeException::class);

        app(ApisPeruService::class)->lookupDni('12345678');
    }
}
