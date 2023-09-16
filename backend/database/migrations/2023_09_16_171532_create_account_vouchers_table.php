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
        Schema::create('account_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vcode')->unique();
            $table->string('invoice_reference', 191)->nullable();
            $table->string('invoice_type', 20)->nullable();
            $table->unsignedBigInteger('cost_center_id');
            $table->string('vnumber')->nullable();
            $table->unsignedBigInteger('vtype_id');
            $table->string('vtype_value', 20);
            $table->string('payment_type', 50)->nullable();
            $table->string('cheque_no', 100)->nullable();
            $table->date('cheque_date')->nullable();
            $table->unsignedBigInteger('fiscal_year_id');
            $table->date('vdate');
            $table->text('global_note')->nullable();
            $table->tinyInteger('entry_level')->default(1)->comment('1=HeadOffice|2=WarehouseBranch|3=OutletBranch');
            $table->tinyInteger('modified_item')->default(2)->comment('0=Only View|1=view & delete|2=view,edit & delete');
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
        Schema::dropIfExists('account_vouchers');
    }
};
