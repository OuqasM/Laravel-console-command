<?php

namespace App\Console\Commands;

use App\Enums\ProductDeletionReason;
use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports products into database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = storage_path('app/products.csv');

        if (!file_exists($filePath)) {
            $this->error("CSV file not found.");
            return 1;
        }

        $contents = file_get_contents($filePath);
        $lines = array_map('str_getcsv', explode("\n", $contents));

        // keep product IDs for synchronzation
        $importedProductIds = [];
        $invalidImportedProductIds = [];

        DB::transaction(function () use ($lines, &$importedProductIds) {
            foreach ($lines as $key => $line) {

                // skip the header
                if ($key === 0) {
                    continue;
                }

                // csv structre: id,name,sku,price,currency,variations,quantity,status
                [$id, $name, $sku, $price, $currency, $variations, $quantity, $status] = array_pad($line, 8, null);

                $importedProductIds[] = $id;

                if (!is_numeric($price) || $price < 0 || $price > 99999.99) {
                    $this->info("Invalid price for row with ID of $key, price:" . $price);
                    continue;
                }

                try {
                    $product = Product::updateOrCreate(
                        ['id' => $id],
                        [
                            'id' => $id,
                            'name' => $name,
                            'sku' => empty($sku) ? null : $sku,
                            'status' => $status,
                            'price' => $price,
                            'currency' => $currency
                        ]
                    );
    
                    if ($variations) {
                        $this->processVariations($product, $variations);
                    }
                } catch (UniqueConstraintViolationException $ex) {
                    $this->info("Duplicate entry for row with ID of $key, please verify ID and SKU"); // todo: work on exception message to retreive wich column is duplicated 
                    continue;
                }
            }

            // soft delete all products not in the csv file and mark their deletion reason
            Product::whereNotIn('id', $importedProductIds)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now(),
                    'deletion_reason' => ProductDeletionReason::SYNCHRONIZATION
                ]);
        });

        $this->info('Product import completed successfully.');
        return 0;
    }

    private function processVariations(Product $product, ?string $variations)
    {
        $product->variations()->delete(); // delete old variations

        if ($variations) {
            $variationsArray = json_decode($variations, true);

            if (is_array($variationsArray)) {
                foreach ($variationsArray as $variation) {
                    $product->variations()->create([
                        'name' => $variation['name'],
                        'value' => $variation['value']
                    ]);
                }
            }
        }
    }
}
