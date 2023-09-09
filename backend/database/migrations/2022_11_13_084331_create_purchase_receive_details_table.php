<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReceiveDetailsTable extends Migration
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
            $table->decimal('receive_purchase_price', 11, 3)->default(0);
            $table->decimal('receive_mrp_price', 11, 3)->default(0);
            $table->decimal('receive_order_quantity')->default(0);
            $table->decimal('receive_quantity');
            $table->decimal('receive_weight')->default(0);
            $table->decimal('receive_product_value', 11, 3)->default(0);
            $table->decimal('receive_free_quantity')->default(0);
            $table->decimal('receive_free_amount', 11, 3)->default(0);
            $table->double('receive_discount_percent', 8, 2)->default(0);
            $table->decimal('receive_discount_amount', 11, 3)->default(0);
            $table->decimal('receive_vat_amount', 11, 3)->default(0);
            $table->decimal('receive_amount', 11, 3)->default(0);
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
}
