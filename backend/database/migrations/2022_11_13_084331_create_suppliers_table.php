<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('type_id')->default(0);
            $table->string('address')->nullable();
            $table->unsignedBigInteger('district_id')->default(0);
            $table->unsignedBigInteger('area_id')->default(0);
            $table->string('postal_code', 10)->nullable();
            $table->string('contact_person_name', 50)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_image')->nullable();
            $table->bigInteger('payable_ledger_id');
            $table->bigInteger('discount_ledger_id');
            $table->bigInteger('advance_ledger_id');
            $table->boolean('outlet_receive')->default(false)->comment('0=No|1=Yes');
            $table->unsignedTinyInteger('payment_terms_conditions')->default(0)->comment('0=Default|1=After Sale|2=Sale After Commission|3=Credit|4=Cash Purchase');
            $table->unsignedInteger('payment_matured_days')->default(0)->comment('Based on after sale');
            $table->double('commission_percent', 6, 2)->default(0)->comment('Based on sale after commission');
            $table->unsignedTinyInteger('supply_schedule')->default(0)->comment('0=Default|1=Daily|2=Weekly|3=Monthly|4=As per requirement');
            $table->tinyInteger('damage_product')->default(0)->comment('0=Default|1=Replace|2=Return');
            $table->tinyInteger('slow_moving_product')->default(0)->comment('0=Default|1=Replace|2=Return');
            $table->tinyInteger('short_dated_product')->default(0)->comment('0=Default|1=Replace|2=Return');
            $table->tinyInteger('expire_product')->default(0)->comment('0=Default|1=Replace|2=Return');
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('routing_no')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->boolean('status')->default(true)->comment('1=Active|0=Inactive');
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
        Schema::dropIfExists('suppliers');
    }
}
