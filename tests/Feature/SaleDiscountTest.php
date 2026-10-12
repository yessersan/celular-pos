<?php

namespace Tests\Feature;

use App\Livewire\SaleManager;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaleDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_discount_is_applied_to_sale_without_changing_product_price(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::create([
            'name' => 'Cargador USB',
            'purchase_price' => 20,
            'sale_price' => 100,
            'stock' => 5,
        ]);

        Livewire::test(SaleManager::class)
            ->set('selectedProduct', $product->id)
            ->set('quantity', 2)
            ->call('addProduct')
            ->set('customer_name', 'Cliente de prueba')
            ->set('discount_percent', '10')
            ->call('saveSale')
            ->assertHasNoErrors();

        $sale = Sale::firstOrFail();
        $this->assertSame('200.00', $sale->subtotal);
        $this->assertSame('20.00', $sale->discount_amount);
        $this->assertSame('180.00', $sale->total);
        $this->assertSame('100.00', $product->fresh()->sale_price);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_discount_above_one_hundred_percent_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::create(['name' => 'Mouse', 'purchase_price' => 10, 'sale_price' => 30, 'stock' => 2]);

        Livewire::test(SaleManager::class)
            ->set('selectedProduct', $product->id)
            ->call('addProduct')
            ->set('customer_name', 'Cliente')
            ->set('discount_percent', '101')
            ->call('saveSale')
            ->assertHasErrors('discount_percent');

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(2, $product->fresh()->stock);
    }
}
