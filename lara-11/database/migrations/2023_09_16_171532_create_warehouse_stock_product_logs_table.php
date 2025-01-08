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
        Schema::create('warehouse_stock_product_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_receive_id')->default(0);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->double('in_stock_quantity')->default(0);
            $table->double('in_stock_weight')->default(0);
            $table->double('stock_quantity')->default(0);
            $table->double('stock_weight')->default(0);
            $table->double('out_stock_quantity')->default(0);
            $table->double('out_stock_weight')->default(0);
            $table->date('expires_date')->nullable();
            $table->string('stock_type', 20)->comment('PR=Purchase Receive|WT=Warehouse Transfer|OT=Outlet Transfer|ADJ=Adjustment Stock');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('warehouse_stock_product_logs');
    }
};
