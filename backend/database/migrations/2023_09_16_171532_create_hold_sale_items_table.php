<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->double('quantity')->default(0);
            $table->double('discount')->default(0);
            $table->double('vat')->default(0);
            $table->integer('vat_id');
            $table->double('mrp_price')->default(0);
            $table->double('cost_price')->default(0);
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
};
