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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number', 20)->default('P00000000000001');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->integer('customer_id');
            $table->string('customer_name');
            $table->double('total_amount')->default(0);
            $table->double('grand_total')->default(0);
            $table->double('collection_amount')->default(0);
            $table->double('paid_amount')->default(0);
            $table->double('return_amount')->default(0);
            $table->string('sale_type', 10);
            $table->string('status', 20);
            $table->integer('courier_id')->default(0);
            $table->double('shipping_charge', 16, 2)->default(0);
            $table->double('customer_discount')->default(0);
            $table->double('customer_group_discount')->nullable()->default(0);
            $table->double('order_discount')->default(0);
            $table->double('order_discount_value')->default(0);
            $table->double('order_vat')->default(0);
            $table->double('order_items_vat')->default(0);
            $table->integer('outlet_id')->default(0);
            $table->string('sale_note')->nullable();
            $table->string('staff_note')->nullable();
            $table->boolean('return_type')->nullable()->default(false)->comment('0=default 1=return 2=replace 3=void');
            $table->tinyInteger('account_post_status')->default(0)->comment('0=Non-posted|1=Partial Posted|2=Posted');
            $table->tinyInteger('inventory_post_status')->default(0)->comment('0=Non-posted|1=Posted');
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('sales');
    }
};
