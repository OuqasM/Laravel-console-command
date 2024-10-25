<?php

namespace App\Services\ProductProviders;

use App\Services\ProductProviders\Classes\ProductObject;
use Illuminate\Support\Collection;

class CsvProvider extends BaseProvider
{

    protected $csvFullPath;

    public function __construct()
    {
    }

    public function setCsVFullPath(string $csvFullPath): self
    {
        $this->csvFullPath = $csvFullPath;

        return $this;
    }

    public function getCsvFullPath(): string
    {
        return $this->csvFullPath;
    }

    public function fetchAllProducts(): array
    {
        $filePath = file_exists($this->getCsvFullPath()) 
            ? $this->getCsvFullPath() 
            : storage_path('app/products.csv');
    
        if (!file_exists($filePath)) {
            throw new \Exception("CSV file not found: " . $filePath);
        }

        $contents = file_get_contents($filePath);
        $rows = array_map('str_getcsv', explode("\n", $contents));

        return array_slice($rows, 1);
    }

    public function mapProduct(array $product): Collection
    {
        $collect = new Collection();
        foreach ($product as $key => $row) {

            // csv structre: id, name, sku, price, currency, variations, quantity, status
            [$id, $name, $sku, $price, $currency, $variations, $quantity, $status] = array_pad($row, 8, null);


            if (!is_numeric($price) || $price < 0 || $price > 99999.99) {
                // $this->comment("Invalid price for row with ID of $key, price:" . $price);
                continue;
            }

            $productObject = new ProductObject();
            $productObject
                ->setId($id)
                ->setName($name)
                ->setSku($sku)
                ->setPrice($price)
                ->setCurrency($currency)
                ->setVariations($variations)
                ->setStatus($status);

            $collect->push($productObject);
        }

        return $collect;
    }
}
