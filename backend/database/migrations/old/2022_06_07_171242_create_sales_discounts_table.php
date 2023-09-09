<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDiscountsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_discounts', function (Blueprint $table) {
            $table->id('id');  
            $table->unsignedBigInteger('sale_item_id'); 
            $table->foreign('sale_item_id')->references('id')->on('sale_items')->onDelete('cascade');
            $table->string('key')->nullable();
            $table->string('value')->nullable();
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
        Schema::drop('sales_discounts');
    }
}
