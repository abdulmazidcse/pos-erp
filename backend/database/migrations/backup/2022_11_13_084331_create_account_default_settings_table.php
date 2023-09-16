<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountDefaultSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_default_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('supplier_payable_account_type');
            $table->unsignedBigInteger('supplier_discount_account_type');
            $table->unsignedBigInteger('supplier_advance_payment_account_type');
            $table->unsignedBigInteger('bank_account_asset_type');
            $table->unsignedBigInteger('bank_loan_account_liability_type');
            $table->unsignedBigInteger('bank_charge_account_expense_type');
            $table->unsignedBigInteger('bank_loan_interest_expense_type');
            $table->unsignedBigInteger('bank_interest_income_type');
            $table->unsignedBigInteger('inventory_account');
            $table->unsignedBigInteger('cogs_account');
            $table->unsignedBigInteger('inventory_adjustment_account');
            $table->unsignedBigInteger('petty_cash_account');
            $table->unsignedBigInteger('cash_in_hand_account'); ///////
            $table->unsignedBigInteger('bank_account');
            $table->unsignedBigInteger('bank_loan_account');
            $table->unsignedBigInteger('bank_loan_interest_expense_account');
            $table->unsignedBigInteger('bank_charge_expense_account');
            $table->unsignedBigInteger('bank_interest_income_account');
            $table->unsignedBigInteger('trade_payable_account');
            $table->unsignedBigInteger('supplier_discount_account');
            $table->unsignedBigInteger('cash_sales_account');
            $table->unsignedBigInteger('mfs_sales_account');
            $table->unsignedBigInteger('bkash_sales_account');
            $table->unsignedBigInteger('rocket_sales_account');
            $table->unsignedBigInteger('nagad_sales_account');
            $table->unsignedBigInteger('card_sales_account');
            $table->unsignedBigInteger('account_receivable_ledger');
            $table->unsignedBigInteger('customer_discount_account');
            $table->unsignedBigInteger('bank_reference_ledger_mfs');
            $table->unsignedBigInteger('mfs_charge_ledger');
            $table->unsignedBigInteger('bank_reference_ledger_bkash');
            $table->unsignedBigInteger('bkash_charge_ledger');
            $table->unsignedBigInteger('bank_reference_ledger_rocket');
            $table->unsignedBigInteger('rocket_charge_ledger');
            $table->unsignedBigInteger('bank_reference_ledger_nagad');
            $table->unsignedBigInteger('nagad_charge_ledger');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_default_settings');
    }
}
