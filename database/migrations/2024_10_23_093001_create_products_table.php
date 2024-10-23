<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->string('status')->nullable();
            $table->decimal('price', 7, 2)->nullable();
            // $table->decimal('quantity')->nullable();
            $table->string('currency', 20)->nullable();
            $table->string('deletion_reason')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
