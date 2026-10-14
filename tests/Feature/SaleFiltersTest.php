<?php

namespace Tests\Feature;

use App\Livewire\SalesReport;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaleFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_can_filter_by_customer_or_receipt_number(): void
    {
        Sale::create(['receipt_number' => 'B001-000001', 'customer_name' => 'Ana Perez', 'total' => 10]);
        Sale::create(['receipt_number' => 'B001-000002', 'customer_name' => 'Luis Soto', 'total' => 20]);

        Livewire::test(SalesReport::class)
            ->set('search', 'Ana')
            ->assertSee('Ana Perez')
            ->assertDontSee('Luis Soto')
            ->set('search', 'B001-000002')
            ->assertSee('Luis Soto')
            ->assertDontSee('Ana Perez');
    }
}
