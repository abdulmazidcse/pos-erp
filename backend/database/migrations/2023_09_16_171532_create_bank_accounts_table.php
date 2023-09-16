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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_no')->unique('accounts_account_no_unique');
            $table->string('bank_name');
            $table->integer('company_id');
            $table->double('initial_balance')->default(0);
            $table->double('current_balance')->default(0);
            $table->double('charge_percent')->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('is_default')->default(0)->comment('0=NotDefault|1=Default');
            $table->unsignedBigInteger('bank_asset_account');
            $table->unsignedBigInteger('bank_loan_account');
            $table->unsignedBigInteger('bank_charge_account');
            $table->unsignedBigInteger('bank_interest_expense_account');
            $table->unsignedBigInteger('bank_interest_income_account');
            $table->tinyInteger('status')->default(1)->comment('1=Active|0=Inactive');
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
        Schema::dropIfExists('bank_accounts');
    }
};
