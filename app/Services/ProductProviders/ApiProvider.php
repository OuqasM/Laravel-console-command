<?php

namespace App\Services\ProductProviders;

use App\Services\Http\HttpClientService;
use App\Services\ProductProviders\Classes\ProductObject;
use Illuminate\Support\Collection;
use Throwable;

class ApiProvider extends BaseProvider
{
    protected HttpClientService $httpClientService;

    public function __construct()
    {
        $this->httpClientService = app()->make(HttpClientService::class);
    }

    public function fetchAllProducts(): array
    {
        $endpoint = 'https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products';

        try {
            $products = $this->httpClientService->getRequest($endpoint);
            
            return $products ?? [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function mapProduct(array $product): Collection
    {
        $collect = new Collection();
        foreach ($product as $key => $row) {
            $productObject = new ProductObject();
            $productObject
                ->setId($row['id'] ?? null)
                ->setName($row['name'] ?? null)
                ->setSku($row['sku'] ?? null)
                ->setPrice($row['price'] ?? null)
                ->setCurrency($row['currency'] ?? null)
                ->setVariations(isset($row['variations']) ? json_encode($row['variations']) : '')
                ->setStatus($row['status'] ?? null);

            $collect->push($productObject);
        }

        return $collect;
    }
}
