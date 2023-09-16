<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold_sale_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sale_item_id');
            $table->integer('product_id');
            $table->decimal('quantity');
            $table->decimal('discount');
            $table->decimal('vat');
            $table->integer('vat_id');
            $table->decimal('mrp_price');
            $table->decimal('cost_price');
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
        Schema::dropIfExists('hold_sale_items');
    }
}
