<?php

namespace App\Services\ProductProviders;

use App\Services\ProductProviders\Classes\ProductObject;

abstract class BaseProvider
{
    abstract public function fetchAllProducts(): array; // since cvs & api dons provide any sort of pagination.. we must load all prodcts on memory
    abstract public function mapProduct(array $product): ProductObject;
}
