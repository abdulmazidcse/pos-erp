<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('order_product_id');
            $table->unsignedInteger('order_product_unit_id')->default(0);
            $table->decimal('order_purchase_price', 11,3)->default(0);
            $table->unsignedInteger('order_quantity');
            $table->decimal('order_product_value')->default(0);
            $table->unsignedTinyInteger('receive_status')->default(0)->comment("0=No Receive|1=Partial Item Receive|2=Partial Quantity Receive|3=Final Received");
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
        Schema::dropIfExists('purchase_order_details');
    }
}
