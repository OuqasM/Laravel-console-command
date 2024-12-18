<?php

namespace App\Console\Commands;

use App\Enums\ProductDeletionReason;
use App\Enums\ProviderSourceTypeEnum;
use App\Jobs\ProcessProductJob;
use App\Jobs\SoftDeleteProductJob;
use App\Models\Product;
use App\Services\ProductProviders\ApiProvider;
use App\Services\ProductProviders\BaseProvider;
use App\Services\ProductProviders\Classes\ProductObject;
use App\Services\ProductProviders\CsvProvider;
use Illuminate\Console\Command;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {source} {--csv_full_path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports products from source (csv or api) into database';

    /**
     * @var BaseProvider $productProvider;
     */
    protected $productProvider;

    /**
     * @var string $source;
     */
    protected $source;

    /**
     * @var string $csvFullPath
     */
    protected $csvFullPath;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->setOptions();
        $this->setProductProviderBaseOnSource();

        $products = $this->productProvider->fetchAllProducts();
        $mappedProducts = $this->productProvider->mapProduct($products);

        $importedProductIds = $mappedProducts->map(function (ProductObject $productObject) {
            return $productObject->getId();
        })->toArray();

        $deletedProductsIds = $mappedProducts->filter(function ($product) {
            return $product->getStatus() === 'deleted'; // also status can be enumed
        })->map(function ($product) {
            return $product->getId();
        })->toArray();

        $this->dispatchProcessProductJobs($mappedProducts);

        $this->softDeleteMissingProductOrMarkedAsDeleted(
            $importedProductIds,
            $deletedProductsIds
        );

        $this->info(count($importedProductIds) . " Products are being processing.");

        return 0;
    }

    private function setOptions(): void
    {
        $this->source = strtolower($this->argument('source'));
        $this->csvFullPath = $this->option('csv_full_path');
    }

    private function setProductProviderBaseOnSource()
    {
        switch ($this->source) {
            case ProviderSourceTypeEnum::CSV->value:
                /**
                 * @var CsvProvider $productProvider
                 */
                $this->productProvider = new CsvProvider();
                $this->productProvider->setCsvFullPath($this->csvFullPath);
                break;
            case ProviderSourceTypeEnum::API->value:
                $this->productProvider = new ApiProvider();
                break;
            default:
                throw new \Exception('Invalid source');
        }
    }

    private function dispatchProcessProductJobs($mappedProducts)
    {
        foreach ($mappedProducts as $product) {
            ProcessProductJob::dispatch($product);
        }
    }

    private function softDeleteMissingProductOrMarkedAsDeleted($importedProductIds, $deletedProductsIds)
    {
        SoftDeleteProductJob::dispatch($importedProductIds, $deletedProductsIds);
    }
}

