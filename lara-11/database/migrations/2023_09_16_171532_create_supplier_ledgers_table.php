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
        Schema::create('supplier_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id');
            $table->bigInteger('purchase_receive_id')->default(0);
            $table->bigInteger('voucher_id')->default(0);
            $table->string('transaction_type')->nullable();
            $table->double('opening_balance')->default(0);
            $table->double('purchase_receive_amount')->default(0);
            $table->double('payment_amount')->default(0);
            $table->double('return_amount')->default(0);
            $table->double('discount_amount')->default(0);
            $table->double('closing_balance')->default(0);
            $table->date('transaction_date');
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
        Schema::dropIfExists('supplier_ledgers');
    }
};
