<?php

namespace Tests\Feature;

use App\Livewire\SaleManager;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaleDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_discount_is_limited_to_sale_total_and_does_not_change_product_price(): void
    {
        $product = Product::create([
            'name' => 'Test phone',
            'purchase_price' => 50,
            'sale_price' => 100,
            'stock' => 5,
        ]);

        Livewire::test(SaleManager::class)
            ->set('items', [[
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'unit_price' => 100,
                'quantity' => 1,
                'subtotal' => 100,
            ]])
            ->set('discount_amount', 20)
            ->call('saveSale')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sales', [
            'subtotal' => 100,
            'discount_amount' => 20,
            'total' => 80,
        ]);
        $this->assertSame('100.00', $product->fresh()->sale_price);
        $this->assertSame(4, $product->fresh()->stock);
    }
}
