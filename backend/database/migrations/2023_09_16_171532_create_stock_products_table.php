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
        Schema::create('stock_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id')->default(0);
            $table->unsignedBigInteger('outlet_id');
            $table->double('in_stock_quantity')->default(0);
            $table->double('in_stock_weight')->default(0);
            $table->double('stock_quantity')->default(0);
            $table->double('stock_weight')->default(0);
            $table->double('out_stock_quantity')->default(0);
            $table->double('out_stock_weight')->nullable()->default(0);
            $table->date('expires_date')->nullable();
            $table->integer('lead_time')->default(0);
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
        Schema::dropIfExists('stock_products');
    }
};
