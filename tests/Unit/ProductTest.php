<?php

namespace Tests\Unit;

use App\Jobs\ProcessProductJob;
use App\Models\Product;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /** @test */
    public function it_can_store_api_variations_to_product()
    {
        $product = Product::factory()->create();
        $product->variations()->createMany([
            [
                'name' => 'size',
                'value' => 'Large',
            ],
            [
                'name' => 'color',
                'value' => 'gray',
            ]
        ]);

        $this->assertCount(2, $product->variations);
    }

    public function test_it_can_dispatch_import_products_job_and_persist_products()
    {
        $products = [
            [
                'name' => 'product1',
                'sku' => 'sku1',
                'status' => 'sale',
                'price' => 100,
                'currency' => 'SAR',
            ],
            [
                'name' => 'product2',
                'sku' => 'sku2',
                'status' => 'hidden',
                'price' => 200,
                'currency' => 'UED',
            ],
        ];

        ProcessProductJob::dispatch($products);

        $this->artisan('queue:work --once');

        $this->assertDatabaseHas('products', [
            'name' => 'product1',
            'sku' => 'sku1',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'product2',
            'sku' => 'sku2',
        ]);
    }
}
