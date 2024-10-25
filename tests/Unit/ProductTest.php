<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

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
}
