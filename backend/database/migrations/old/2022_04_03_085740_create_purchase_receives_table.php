<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReceivesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_receives', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('purchase_order_id')->default(0)->comment("0=Direct Purchase");
            $table->unsignedBigInteger('supplier_id')->default(0)->comment("0=Multiple Supplier");
            $table->string('receive_type')->nullable();
            $table->string('reference_no');
            $table->string('challan_no')->nullable();
            $table->date('receive_date');
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->unsignedBigInteger('outlet_id')->default(0);
            $table->double('total_rcv_quantity', 8,2)->default(0);
            $table->double('total_rcv_value', 16, 3)->default(0);
            $table->double('total_free_quantity', 8, 2)->default(0);
            $table->double('total_free_amount', 11, 3)->default(0);
            $table->double('total_commission_value', 11, 3)->default(0);
            $table->double('total_vat', 11, 3)->default(0);
            $table->double('total_amount', 16, 3)->default(0);
            $table->double('additional_cost', 11, 3)->default(0);
            $table->double('additional_discount', 11, 3)->default(0);
            $table->double('net_amount', 16, 3)->default(0);
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
        Schema::drop('purchase_receives');
    }
}
