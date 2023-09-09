<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockProductsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_products_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_receive_id')->default(0);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('outlet_id');
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
        Schema::dropIfExists('stock_products_logs');
    }
}
