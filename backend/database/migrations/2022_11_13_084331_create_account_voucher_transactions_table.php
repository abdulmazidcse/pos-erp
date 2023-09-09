<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountVoucherTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_voucher_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('voucher_id');
            $table->string('cost_center_id');
            $table->unsignedBigInteger('ledger_id');
            $table->string('ledger_code');
            $table->string('vaccount_type')->nullable();
            $table->string('vtransaction_type')->nullable();
            $table->decimal('debit', 16, 4);
            $table->decimal('credit', 16, 4);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedTinyInteger('transaction_sl');
            $table->text('voucher_note')->nullable();
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
        Schema::dropIfExists('account_voucher_transactions');
    }
}
