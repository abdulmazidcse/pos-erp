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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_code')->unique();
            $table->unsignedTinyInteger('customer_group_id')->default(1);
            $table->string('name');
            $table->string('email', 125)->nullable();
            $table->string('phone')->unique();
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('address');
            $table->date('dob')->nullable();
            $table->unsignedInteger('district_id')->default(0);
            $table->unsignedInteger('area_id')->default(0);
            $table->string('postal_code', 10)->nullable();
            $table->double('discount_percent');
            $table->boolean('wholesale_customer')->default(false);
            $table->boolean('sale_without_vat')->default(false);
            $table->boolean('credit_customer')->default(false);
            $table->boolean('store_customer')->default(false);
            $table->bigInteger('receivable_ledger_id');
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
        Schema::dropIfExists('customers');
    }
};
