<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreRequisitionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_requisition_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_requisition_id');
            $table->unsignedBigInteger('requisition_product_id');
            $table->unsignedInteger('requisition_quantity');
            $table->unsignedInteger('approve_quantity');
            $table->unsignedTinyInteger('status')->default(1)->comment('0=Inactive|1=Active');
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
        Schema::dropIfExists('store_requisition_details');
    }
}
