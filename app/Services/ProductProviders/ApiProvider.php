<?php

namespace App\Services\ProductProviders;

use App\Services\Http\HttpClientService;
use App\Services\ProductProviders\Classes\ProductObject;

class ApiProvider extends BaseProvider
{
    protected HttpClientService $httpClientService;

    public function __construct(HttpClientService $httpClientService)
    {
        $this->httpClientService = $httpClientService;
    }

    public function fetchAllProducts(): array
    {
        return [];

    }

    public function mapProduct(array $product): ProductObject
    {
        return new ProductObject();
    }
}
