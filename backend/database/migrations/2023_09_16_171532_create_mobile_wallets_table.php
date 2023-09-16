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
        Schema::create('mobile_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mobile_wallet', 50);
            $table->string('agent_name', 100);
            $table->string('mobile_number', 15);
            $table->unsignedBigInteger('company_id')->default(0);
            $table->double('charge_percent')->default(0);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('mobile_wallets');
    }
};
