<?php

namespace App\Jobs;

use App\Enums\ProductDeletionReason;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SoftDeleteProductJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var array $importedProductIds
     */
    protected $importedProductIds;

    /**
     * @var array $deletedProductsIds
     */
    protected $deletedProductsIds;

    public function __construct(array $importedProductIds, array $deletedProductsIds)
    {
        $this->importedProductIds = $importedProductIds;
        $this->deletedProductsIds = $deletedProductsIds;
    }

    public function handle(): void
    {
        Product::where(function ($query) {
            $query->whereNotIn('id', $this->importedProductIds)
                  ->orWhereIn('id', $this->deletedProductsIds);
        })
        ->whereNull('deleted_at') // to skip prds already soft deleted
        ->update([
            'deleted_at' => now(),
            'deletion_reason' => ProductDeletionReason::SYNCHRONIZATION
        ]);
    }
}
