<?php

namespace App\Console\Commands;

use App\Enums\ProductDeletionReason;
use App\Jobs\ProcessProductJob;
use Illuminate\Console\Command;
use App\Models\Product;

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
    protected $description = 'Imports products from CSV file into database';

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
        $rows = array_map('str_getcsv', explode("\n", $contents));

        // keep product IDs for synchronzation
        $importedProductIds = [];

        foreach ($rows as $key => $row) {
            // skip the header
            if ($key === 0) {
                continue;
            }

            // csv structre: id, name, sku, price, currency, variations, quantity, status
            [$id, $name, $sku, $price, $currency, $variations, $quantity, $status] = array_pad($row, 8, null);

            $importedProductIds[] = $id;

            if (!is_numeric($price) || $price < 0 || $price > 99999.99) {
                $this->comment("Invalid price for row with ID of $key, price:" . $price);
                continue;
            }

            ProcessProductJob::dispatch([$id, $name, $sku, $price, $currency, $variations, $quantity, $status]);
        }

        // soft delete all products not in the csv file and mark their deletion reason
        Product::whereNotIn('id', $importedProductIds)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'deletion_reason' => ProductDeletionReason::SYNCHRONIZATION
            ]);

        $this->info('Products is being processing.');
        return 0;
    }
}
