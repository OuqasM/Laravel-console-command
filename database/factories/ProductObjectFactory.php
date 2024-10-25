<?php

namespace Database\Factories;

use App\Services\ProductProviders\Classes\ProductObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductObject>
 */
class ProductObjectFactory extends Factory
{
    protected $model = ProductObject::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'sku' => $this->faker->unique()->ean8,
            'status' => $this->faker->randomElement(['hidden', 'deleted', 'sale']),
            'price' => $this->faker->randomFloat(2, 1, 9999),
            'currency' => $this->faker->currencyCode,
            'deletion_reason' => null,
            'deleted_at' => null
        ];
    }
}
