<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('id');
            $table->string('reference_no');
            $table->unsignedBigInteger('store_requisition_id')->default(0);
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_payment_type')->nullable();
            $table->date('order_date');
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->unsignedBigInteger('outlet_id')->default(0);
            $table->integer('total_order_quantity')->default(0);
            $table->double('total_product_value')->default(0);
            $table->double('commission_value')->default(0);
            $table->tinyInteger('approve_status')->default(0)->comment('0=Pending|1=Approved|2=Reject');
            $table->tinyInteger('receive_status')->default(0)->comment('0=No Receive|1=Received|2=Partially Receive');
            $table->text('remarks')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Hold|1=Active');
            $table->unsignedBigInteger('user_id')->default(0);
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
        Schema::drop('purchase_orders');
    }
}
