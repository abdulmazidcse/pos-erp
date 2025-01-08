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
        Schema::create('purchase_receive_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_receive_id');
            $table->unsignedBigInteger('order_details_id')->default(0);
            $table->unsignedBigInteger('receive_supplier_id')->default(0)->comment('0=Multiple Supplier');
            $table->unsignedBigInteger('receive_product_id');
            $table->unsignedBigInteger('receive_product_unit_id')->default(0);
            $table->double('receive_purchase_price')->default(0);
            $table->double('receive_mrp_price')->default(0);
            $table->double('receive_order_quantity')->default(0);
            $table->double('receive_quantity')->default(0);
            $table->double('receive_weight')->default(0);
            $table->double('receive_product_value')->default(0);
            $table->double('receive_free_quantity')->default(0);
            $table->double('receive_free_amount')->default(0);
            $table->double('receive_discount_percent')->default(0);
            $table->double('receive_discount_amount')->default(0);
            $table->double('receive_vat_amount')->default(0);
            $table->double('receive_amount')->default(0);
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
        Schema::dropIfExists('purchase_receive_details');
    }
};
