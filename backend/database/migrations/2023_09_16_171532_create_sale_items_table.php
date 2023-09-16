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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sale_id');
            $table->integer('stock_id');
            $table->integer('product_id');
            $table->double('quantity')->default(0);
            $table->double('discount')->default(0);
            $table->double('vat')->default(0);
            $table->integer('vat_id');
            $table->double('mrp_price')->nullable()->default(0);
            $table->double('cost_price')->default(0);
            $table->boolean('return_type')->nullable()->default(false)->comment('0=default 1=return 2=replace 3=void');
            $table->integer('uom')->nullable()->comment('UOM = unit of measure');
            $table->double('weight')->nullable()->default(0)->comment('Kilogram ');
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
};
