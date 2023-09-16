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
            $table->bigIncrements('id');
            $table->string('reference_no');
            $table->string('store_requisition_id')->nullable();
            $table->string('challan_no')->nullable();
            $table->date('challan_date')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_payment_type')->nullable();
            $table->string('number_of_po')->nullable();
            $table->string('supply_schedule')->nullable();
            $table->date('order_date');
            $table->date('delivery_date');
            $table->unsignedBigInteger('delivery_to_outlet')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('total_qty')->default(0);
            $table->double('total_value')->default(0);
            $table->double('commission_value')->default(0);
            $table->double('total_vat')->default(0);
            $table->double('total_free_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->tinyInteger('approve_status')->default(0)->comment('0=Pending|1=Approved|2=Reject');
            $table->tinyInteger('receive_status')->default(0)->comment('0=No Receive|1=Received|2=Partially Received');
            $table->text('remarks')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Hold|1=Active');
            $table->unsignedBigInteger('warehouse_id')->default(0);
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
        Schema::dropIfExists('purchase_orders');
    }
}
