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
        Schema::create('store_requisitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('requisition_no')->unique();
            $table->date('requisition_date');
            $table->unsignedBigInteger('outlet_id');
            $table->double('total_quantity')->default(0);
            $table->double('total_value')->default(0);
            $table->double('total_amount')->default(0);
            $table->tinyInteger('approve_status')->default(0)->comment('0=pending|1=Approved|2=Reject');
            $table->tinyInteger('order_status')->default(0)->comment('0=Pending|1=Partial|2=Complete');
            $table->text('remarks')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Inactive|1=Active');
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->unsignedBigInteger('user_id')->default(0);
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
        Schema::dropIfExists('store_requisitions');
    }
};
