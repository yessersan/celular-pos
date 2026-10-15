<?php

namespace Tests\Feature;

use App\Livewire\ProductManager;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_product_search_filters_by_name_and_stock(): void
    {
        Product::create(['name' => 'Cargador USB', 'purchase_price' => 10, 'sale_price' => 25, 'stock' => 8]);
        Product::create(['name' => 'Cable HDMI', 'purchase_price' => 5, 'sale_price' => 12, 'stock' => 0]);

        Livewire::test(ProductManager::class)
            ->set('search', 'Cargador')
            ->assertSee('Cargador USB')
            ->assertDontSee('Cable HDMI')
            ->set('search', '')
            ->set('stockFilter', 'out')
            ->assertSee('Cable HDMI')
            ->assertDontSee('Cargador USB');
    }

    public function test_price_range_filters_products(): void
    {
        Product::create(['name' => 'Cable económico', 'purchase_price' => 2, 'sale_price' => 8, 'stock' => 5]);
        Product::create(['name' => 'Cargador premium', 'purchase_price' => 20, 'sale_price' => 80, 'stock' => 5]);

        Livewire::test(ProductManager::class)
            ->set('minPrice', '50')
            ->assertSee('Cargador premium')
            ->assertDontSee('Cable económico');
    }

    public function test_sell_button_redirects_to_sale_with_selected_product(): void
    {
        $product = Product::create(['name' => 'Adaptador USB-C', 'purchase_price' => 10, 'sale_price' => 30, 'stock' => 4]);

        Livewire::test(ProductManager::class)
            ->call('sell', $product->id)
            ->assertRedirect(route('sales', ['product' => $product->id]));
    }
}
