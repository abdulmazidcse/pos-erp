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
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('product_unit_id')->default(0);
            $table->double('purchase_price')->default(0);
            $table->double('order_quantity')->default(0);
            $table->double('receive_quantity')->default(0);
            $table->double('discount_percent')->default(0);
            $table->double('free_quantity')->default(0);
            $table->double('product_value')->default(0);
            $table->double('discount_amount')->default(0);
            $table->double('free_amount')->default(0);
            $table->double('vat_amount')->default(0);
            $table->double('amount')->default(0);
            $table->text('line_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_products');
    }
};
