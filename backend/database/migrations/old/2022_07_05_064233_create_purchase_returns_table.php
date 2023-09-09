<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 20);
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('outlet_id')->default(0);
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->decimal('total_return_quantity', 8,2)->default(0);
            $table->decimal('total_return_amount', 11, 3)->default(0);
            $table->date('return_date');
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('purchase_returns');
    }
}
