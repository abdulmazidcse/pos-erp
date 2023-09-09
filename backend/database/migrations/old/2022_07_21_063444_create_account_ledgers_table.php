<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountLedgersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_ledgers', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('ledger_code')->unique();
            $table->string('ledger_name');
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('detail_type_id');
            $table->unsignedTinyInteger('is_control_transaction')->default(0)->comment('0=No|1=Yes');
            $table->unsignedTinyInteger('is_master_head')->default(0)->comment('0=No|1=Yes');
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
        Schema::drop('account_ledgers');
    }
}
