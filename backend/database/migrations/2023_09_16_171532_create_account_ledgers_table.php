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
        Schema::create('account_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ledger_code')->unique();
            $table->string('ledger_name');
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('detail_type_id');
            $table->enum('ledger_type', ['dr', 'cr']);
            $table->double('opening_balance')->default(0);
            $table->date('balance_date')->nullable();
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
        Schema::dropIfExists('account_ledgers');
    }
};
