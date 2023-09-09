<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_return_id');
            $table->unsignedBigInteger('receive_details_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_unit_id');
            $table->date('return_expire_date')->nullable();
            $table->decimal('return_purchase_price', 11, 3);
            $table->decimal('return_quantity', 8,2);
            $table->decimal('return_amount', 11,3);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('purchase_return_details');
    }
}
