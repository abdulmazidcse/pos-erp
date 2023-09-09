<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_type',15); 
            $table->string('product_name',150); 
            $table->string('product_native_name',150); 
            $table->string('product_code',150); 
            $table->integer('category_id')->unsigned()->nullable(); 
            $table->integer('sub_category_id')->unsigned()->nullable(); 
            $table->integer('sub_sub_category_id')->unsigned()->nullable(); 
            $table->integer('brand_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            
            $table->string('barcode_symbology',15)->nullable(); 
            $table->integer('min_order_qty')->unsigned()->default(0);
            $table->double('cost_price')->unsigned()->nullable();
            $table->double('depo_price')->unsigned()->nullable();
            $table->double('mrp_price')->unsigned()->nullable();
            $table->unsignedTinyInteger('tax_method')->default(1)->comment('1=Exclusive|2=Inclusive'); 
            $table->integer('product_tax')->unsigned()->nullable(); 
            $table->integer('alert_quantity')->unsigned()->nullable(); 
            $table->text('thumbnail')->nullable();
            $table->text('supplier_id')->nullable();

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();  
            $table->unsignedTinyInteger('is_ecommerce')->default(1)->comment('0=No|1=Yes');
            $table->unsignedTinyInteger('is_expirable')->default(1)->comment('0=No|1=Yes');
            
            $table->integer('purchase_measuring_unit')->unsigned()->nullable();
            $table->integer('sales_measuring_unit')->unsigned()->nullable();
            $table->integer('convertion_rate')->unsigned()->nullable();
            $table->integer('carton_size')->unsigned()->nullable();
            $table->integer('carton_cpu')->unsigned()->nullable();

            $table->tinyInteger('allow_checkout_when_out_of_stock')->unsigned()->default(1)->comment('0=No|1=Yes');
            $table->unsignedTinyInteger('is_outlet_management')->default(0)->comment('0=No|1=Yes');  
            $table->integer('outlet_id')->unsigned()->nullable();            
            $table->integer('quantity')->unsigned()->nullable();             
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
        Schema::drop('products');
    }
}
