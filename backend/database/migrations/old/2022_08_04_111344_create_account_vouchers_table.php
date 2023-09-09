<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountVouchersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_vouchers', function (Blueprint $table) {
            $table->id('id');
            $table->string('vcode')->unique();
            $table->string('vnumber')->unique();
            $table->unsignedBigInteger('vtype_id');
            $table->string('vtype_value', 20);
            $table->unsignedBigInteger('fiscal_year_id');
            $table->date('vdate');
            $table->text('global_note')->nullable();
            $table->tinyInteger('entry_level')->default(1)->comment("1=HeadOffice|2=WarehouseBranch|3=OutletBranch");
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
        Schema::drop('account_vouchers');
    }
}
