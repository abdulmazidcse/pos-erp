<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldSalesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('hold_sales', function (Blueprint $table) {
            $table->id('id');
            $table->integer('customer_id'); 
            $table->string('customer_name');
            $table->string('invoice_number', 16,2)->unique();
            $table->double('total_amount', 16,2)->default(0);
            $table->double('grand_total', 16,2)->default(0);
            $table->double('collection_amount', 16,2)->default(0);
            $table->double('paid_amount', 16,2)->default(0);
            $table->double('return_amount', 16,2)->default(0);
            $table->string('sale_type',10);
            $table->string('status',20);
            $table->integer('courier_id')->default(0);
            $table->double('shipping_charge', 16,2)->default(0);  
            $table->string('order_discount',20)->default(0);  
            $table->double('order_discount_value', 16,2)->default(0);  
            $table->double('order_vat', 16,2)->default(0);
            $table->double('order_items_vat', 16,2)->default(0);
            $table->integer('outlet_id')->default(0);
            $table->string('sale_note')->nullable();
            $table->string('staff_note')->nullable();
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
        Schema::drop('hold_sales');
    }
}
