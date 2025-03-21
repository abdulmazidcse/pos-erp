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
        Schema::create('order_requisition_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_order_id')->default(0);
            $table->unsignedBigInteger('purchase_requisition_id')->default(0);
            $table->unsignedBigInteger('purchase_receive_id')->default(0);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_unit_id')->default(0);
            $table->double('order_purchase_price')->default(0);
            $table->double('requisition_purchase_price')->default(0);
            $table->double('receive_purchase_price')->default(0);
            $table->double('order_quantity')->default(0);
            $table->double('requisition_quantity')->default(0);
            $table->double('approve_quantity')->default(0);
            $table->double('receive_quantity')->default(0);
            $table->double('order_discount_percent')->default(0);
            $table->double('receive_discount_percent')->default(0);
            $table->double('order_discount_amount')->default(0);
            $table->double('receive_discount_amount')->default(0);
            $table->double('order_free_quantity')->default(0);
            $table->double('receive_free_quantity')->default(0);
            $table->double('order_free_amount')->default(0);
            $table->double('receive_free_amount')->default(0);
            $table->double('order_product_value')->default(0);
            $table->double('requisition_product_value')->default(0);
            $table->double('receive_product_value')->default(0);
            $table->double('order_vat_amount')->default(0);
            $table->double('requisition_vat_amount')->default(0);
            $table->double('receive_vat_amount')->default(0);
            $table->double('order_amount')->default(0);
            $table->double('requisition_amount')->default(0);
            $table->double('receive_amount')->default(0);
            $table->text('order_line_notes')->nullable();
            $table->text('requisition_line_notes')->nullable();
            $table->text('receive_line_notes')->nullable();
            $table->tinyInteger('order_status')->default(0)->comment('0=Pending|1=Partial|2=Complete');
            $table->tinyInteger('receive_status')->default(0)->comment('0=Pending|1=Partial|2=Complete');
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
        Schema::dropIfExists('order_requisition_details');
    }
};
