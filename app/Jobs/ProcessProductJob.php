<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Queue\Queueable;

class ProcessProductJob implements ShouldQueue
{
    use Queueable;

    protected $productData;

    public function __construct($productData)
    {
        $this->productData = $productData;
    }

    public function handle(): void
    {
        [$id, $name, $sku, $price, $currency, $variations, $quantity, $status] = $this->productData;
        try {
            
                $product = Product::find($id);
                if (!$product) {
                    $product = Product::create([
                        'id' => $id,
                        'name' => $name,
                        'sku' => empty($sku) ? null : $sku,
                        'status' => $status,
                        'price' => $price,
                        'currency' => $currency
                    ]);
                } else {
                    if ($this->shouldUpdateProduct($product, $price, $status, $name, $sku, $currency, $quantity, $variations)) {
                        $product->update([
                            'name' => trim($name),
                            'sku' => empty($sku) ? null : trim($sku),
                            'status' => trim($status),
                            'price' => trim($price),
                            'currency' => trim($currency)
                        ]);
                    }
                }

            if ($variations) {
                $this->processVariations($product, $variations);
            }
        } catch (QueryException $ex) { // could be thrown bcuz of the sku unique constraint, invalid column type..
            throw $ex; // we can log the execption or notify the admin
            // throw it again or just do not catch it to let us have the possibility to retry the failed job
        }
    }

    private function processVariations(Product $product, ?string $variations)
    {
        $product->variations()->delete(); // delete old variations
    
        if ($variations) {
            $variationsArray = json_decode($variations, true);
    
            if (is_array($variationsArray)) {
                foreach ($variationsArray as $variation) {
                    if (isset($variation['name']) && isset($variation['value'])) {
                        $product->variations()->create([
                            'name' => $variation['name'],
                            'value' => $variation['value']
                        ]);
                    } else {
                        foreach ($variation as $name => $value) {
                            $product->variations()->create([
                                'name' => $name,
                                'value' => $value
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function shouldUpdateProduct(Product $product, $price, $status, $name, $sku, $currency, $quantity): bool
    {
        // the variations check is a little bit heavy so we just update the variations however
        return $product->name !== $name
            || $product->sku !== $sku
            || $product->status !== $status
            || $product->price !== $price
            || $product->currency !== $currency
            || $product->quantity !== $quantity;
    }
}
