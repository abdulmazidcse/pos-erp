<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLedgersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sale_id');
            $table->string('transaction_type');
            $table->string('note');
            $table->double('opening_balance');
            $table->double('debit_amount');
            $table->double('credit_amount');  
            $table->double('closing_balance');
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
        Schema::drop('customer_ledgers');
    }
}
