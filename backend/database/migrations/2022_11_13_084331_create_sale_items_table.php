<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sale_id');
            $table->integer('stock_id');
            $table->integer('product_id');
            $table->decimal('quantity');
            $table->decimal('discount');
            $table->decimal('vat');
            $table->integer('vat_id');
            $table->decimal('mrp_price');
            $table->decimal('cost_price');
            $table->boolean('return_type')->nullable()->default(false)->comment('0=default 1=return 2=replace 3=void');
            $table->integer('uom')->nullable()->comment('UOM = unit of measure');
            $table->decimal('weight', 10)->nullable()->comment('Kilogram ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
}
