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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_no', 20);
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('outlet_id')->default(0);
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->double('total_return_quantity')->default(0);
            $table->double('total_return_amount')->default(0);
            $table->date('return_date');
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('user_id')->default(0);
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
        Schema::dropIfExists('purchase_returns');
    }
};
