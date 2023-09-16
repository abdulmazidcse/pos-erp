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
        Schema::create('purchase_receives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('receive_type')->nullable();
            $table->string('reference_no');
            $table->string('challan_no')->nullable();
            $table->date('purchase_date');
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->unsignedBigInteger('outlet_id')->default(0);
            $table->unsignedBigInteger('delivery_to')->default(0);
            $table->double('total_rcv_quantity')->default(0);
            $table->double('total_rcv_weight')->default(0);
            $table->double('total_rcv_value')->default(0);
            $table->double('total_commission_value')->default(0);
            $table->double('total_vat')->default(0);
            $table->double('total_free_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('additional_discount')->default(0);
            $table->double('additional_cost')->default(0);
            $table->double('net_amount')->default(0);
            $table->double('paid_amount')->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', ''])->default('pending');
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Inactive|1=Active|2=Hold');
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
        Schema::dropIfExists('purchase_receives');
    }
};
