<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductProviders\Classes\ProductObject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class ProcessProductJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var ProductObject $productObject
     */
    protected $productObject;

    public function __construct($productObject)
    {
        $this->productObject = $productObject;
    }

    public function handle(): void
    {
        try {
            
                $product = Product::find($this->productObject->getId());
                if (!$product) {
                    $product = Product::create([
                        'id' => $this->productObject->getId(),
                        'name' => $this->productObject->getName(),
                        'sku' => empty($this->productObject->getSku()) ? null : $this->productObject->getSku(),
                        'status' => $this->productObject->getStatus(),
                        'price' => $this->productObject->getPrice(),
                        'currency' => $this->productObject->getCurrency(),
                    ]);
                } else {
                    if ($this->shouldUpdateProduct($product, $this->productObject)) {
                        $product->update([
                            'name' => trim($this->productObject->getName()),
                            'sku' => empty($this->productObject->getSku()) ? null : trim($this->productObject->getSku()),
                            'status' => trim($this->productObject->getStatus()),
                            'price' => trim($this->productObject->getPrice()),
                            'currency' => trim($this->productObject->getCurrency())
                        ]);
                    }
                }

            if ($variations = $this->productObject->getVariations()) {
                $this->processVariations($product, $variations);
            }
        } catch (QueryException $ex) { // could be thrown bcuz of the sku unique constraint, invalid column type..
            // throw $ex; // we can log the execption or notify the admin
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

    private function shouldUpdateProduct(Product $product, ProductObject $productObject): bool
    {
        // the variations check is a little bit heavy so we just update the variations however
        return $product->name !== $productObject->getName()
            || $product->sku !== $productObject->getSku()
            || $product->status !== $productObject->getStatus()
            || $product->price !== $productObject->getPrice()
            || $product->currency !== $productObject->getCurrency();
    }
}
