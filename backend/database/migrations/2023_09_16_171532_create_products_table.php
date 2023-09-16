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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_type', 15);
            $table->string('product_name', 150);
            $table->string('product_native_name', 150);
            $table->string('product_code', 150);
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('sub_category_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->string('barcode_symbology', 15)->nullable();
            $table->unsignedInteger('min_order_qty')->default(0);
            $table->double('cost_price')->nullable()->default(0);
            $table->double('depo_price')->nullable()->default(0);
            $table->double('mrp_price')->nullable()->default(0);
            $table->double('abp_price')->nullable()->default(0);
            $table->unsignedTinyInteger('tax_method')->default(1)->comment('1=Exclusive|2=Inclusive');
            $table->unsignedInteger('product_tax')->nullable();
            $table->double('discount')->nullable()->default(0);
            $table->unsignedInteger('alert_quantity')->nullable();
            $table->text('thumbnail')->nullable();
            $table->text('supplier_id')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedTinyInteger('is_ecommerce')->nullable()->default(1)->comment('0=No|1=Yes');
            $table->unsignedTinyInteger('is_expirable')->nullable()->default(1)->comment('0=No|1=Yes');
            $table->unsignedInteger('purchase_measuring_unit')->nullable();
            $table->unsignedInteger('sales_measuring_unit')->nullable();
            $table->unsignedInteger('convertion_rate')->nullable();
            $table->unsignedInteger('carton_size')->nullable();
            $table->unsignedInteger('carton_cpu')->nullable();
            $table->unsignedTinyInteger('allow_checkout_when_out_of_stock')->default(1)->comment('0=No|1=Yes');
            $table->unsignedTinyInteger('is_outlet_management')->default(0)->comment('0=No|1=Yes');
            $table->unsignedInteger('outlet_id')->nullable();
            $table->double('quantity')->nullable()->default(0);
            $table->double('abp_qty')->nullable()->default(0);
            $table->unsignedInteger('return_policy')->default(0);
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
        Schema::dropIfExists('products');
    }
};
