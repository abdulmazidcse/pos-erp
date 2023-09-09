<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductExpiresDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_expires_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_receive_details_id');
            $table->unsignedBigInteger('product_id');
            $table->date('expire_date');
            $table->integer('expire_quantity');
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
        Schema::dropIfExists('product_expires_dates');
    }
}
