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
        Schema::create('payment_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sale_id');
            $table->string('paying_by', 20);
            $table->double('amount')->default(0);
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->string('card_reference_no', 191)->nullable();
            $table->string('transaction_no', 191)->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->text('payment_note')->nullable();
            $table->tinyInteger('post_status')->default(0)->comment('0=Non-posted|1=Posted');
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
        Schema::dropIfExists('payment_collections');
    }
};
