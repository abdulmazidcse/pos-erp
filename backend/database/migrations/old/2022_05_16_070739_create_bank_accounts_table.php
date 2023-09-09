<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id('id');
            $table->string('account_no')->unique();
            $table->string('bank_name');
            $table->Integer('company_id');
            $table->decimal('initial_balance', 12,2)->default(0);
            $table->decimal('current_balance')->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('is_default')->default(0)->comment('0=NotDefault|1=Default');
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
        Schema::drop('bank_accounts');
    }
}
