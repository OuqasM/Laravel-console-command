<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_import_csv_products(): void
    {
        $this->artisan("import:products csv --csv_full_path=./products_test.csv");

        $this->artisan("queue:work --once");

        $this->assertDatabaseHas('products', [
            'id' => 61,
            'id' => 73
        ]);

        $this->assertSoftDeleted('products', [
            'id' => 70
        ]);
    }


    /** @test */
    public function it_can_import_api_products(): void
    {
        $this->artisan("import:products api");

        $this->artisan("queue:work --once");

        $this->assertDatabaseCount('products', 100);
    }
}
