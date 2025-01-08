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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_no');
            $table->string('transfer_type');
            $table->unsignedBigInteger('from_warehouse_id')->default(0);
            $table->unsignedBigInteger('to_warehouse_id')->default(0);
            $table->unsignedBigInteger('from_outlet_id')->default(0);
            $table->unsignedBigInteger('to_outlet_id')->default(0);
            $table->integer('total_item');
            $table->double('total_quantity')->default(0);
            $table->double('total_weight')->default(0);
            $table->double('total_cost')->default(0);
            $table->double('shipping_cost')->default(0);
            $table->double('grand_total')->default(0);
            $table->text('documents')->nullable();
            $table->text('note')->nullable();
            $table->date('transfer_date')->nullable();
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
        Schema::dropIfExists('stock_transfers');
    }
};
