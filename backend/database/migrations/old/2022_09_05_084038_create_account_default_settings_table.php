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
            $table->id();
            $table->unsignedBigInteger('supplier_payable_account_type');
            $table->unsignedBigInteger('supplier_discount_account_type');
            $table->unsignedBigInteger('supplier_advance_payment_account_type');
            $table->unsignedBigInteger('bank_account_type');
            $table->unsignedBigInteger('inventory_account');
            $table->unsignedBigInteger('cogs_account');
            $table->unsignedBigInteger('inventory_adjustment_account');
            $table->unsignedBigInteger('petty_cash_account');
            $table->unsignedBigInteger('cash_in_hand_account');
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
