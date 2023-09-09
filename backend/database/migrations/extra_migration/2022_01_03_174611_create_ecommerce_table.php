<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('status', 60)->default('published');
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->integer('company_id')->unsigned()->nullable();
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('parent_id')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('status', 60)->default('published');
            $table->integer('order')->unsigned()->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('description', 400)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('symbol', 10);
            $table->tinyInteger('is_prefix_symbol')->unsigned()->default(0);
            $table->tinyInteger('decimals')->unsigned()->default(0)->nullable();
            $table->integer('order')->default(0)->unsigned()->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->double('exchange_rate')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable(); 
            $table->string('status', 60)->default('published');
            $table->text('images')->nullable();
            $table->string('sku')->nullable();
            $table->integer('order')->unsigned()->default(0);
            $table->integer('quantity')->unsigned()->nullable();
            $table->tinyInteger('allow_checkout_when_out_of_stock')->unsigned()->default(0);
            $table->tinyInteger('with_storehouse_management')->unsigned()->default(0);
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->text('options')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('brand_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('outlet_id')->unsigned()->nullable();
            $table->tinyInteger('is_variation')->default(0);
            $table->tinyInteger('is_searchable')->default(0);
            $table->tinyInteger('is_show_on_list')->default(0);
            $table->tinyInteger('sale_type')->default(0);
            $table->double('price')->unsigned()->nullable();
            $table->double('sale_price')->unsigned()->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->float('length')->nullable();
            $table->float('wide')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('barcode')->nullable();
            $table->string('length_unit', 20)->nullable();
            $table->string('wide_unit', 20)->nullable();
            $table->string('height_unit', 20)->nullable();
            $table->string('weight_unit', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_category_product', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
        });

        Schema::create('product_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 400)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_tag_product', function (Blueprint $table) {
            $table->integer('product_id')->unsigned()->index();
            $table->integer('tag_id')->unsigned()->index();

            $table->primary(['product_id', 'tag_id']);
        });

        Schema::create('product_collection_products', function (Blueprint $table) {
            $table->id();
            $table->integer('product_collection_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
        });

        Schema::create('product_attribute_sets', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('slug', 120)->nullable();
            $table->string('display_layout')->default('swatch_dropdown');
            $table->tinyInteger('is_searchable')->unsigned()->default(1);
            $table->tinyInteger('is_comparable')->unsigned()->default(1);
            $table->tinyInteger('is_use_in_product_listing')->unsigned()->default(0);
            $table->string('status', 60)->default('published');
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->integer('attribute_set_id')->unsigned();
            $table->string('title', 120);
            $table->string('slug', 120)->nullable();
            $table->string('color', 50);
            $table->string('image');
            $table->tinyInteger('is_default')->unsigned()->default(0);
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->string('status', 60)->default('published');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_with_attribute_set', function (Blueprint $table) {
            $table->id();
            $table->integer('attribute_set_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->tinyInteger('order')->unsigned()->default(0);
        });

        Schema::create('product_with_attribute', function (Blueprint $table) {
            $table->id();
            $table->integer('attribute_id')->unsigned();
            $table->integer('product_id')->unsigned();
        });

        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('configurable_product_id')->unsigned();
            $table->tinyInteger('is_default')->default(0);
        });

        Schema::create('product_variation_items', function (Blueprint $table) {
            $table->id();
            $table->integer('attribute_id')->unsigned();
            $table->integer('variation_id')->unsigned();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('post_code')->nullable();
            $table->float('percentage', 8, 6)->nullable();
            $table->integer('priority')->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->float('star');
            $table->string('comment');
            $table->string('status', 60)->default('published');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipping', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('country', 120)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('shipping_option', 60)->nullable();
            $table->string('shipping_method', 60)->default('default');
            $table->string('status', 120)->default('pending');
            $table->decimal('amount', 15);
            $table->integer('currency_id')->unsigned()->nullable();
            $table->decimal('tax_amount')->nullable();
            $table->decimal('shipping_amount')->nullable();
            $table->text('description')->nullable();
            $table->string('coupon_code', 120)->nullable();
            $table->decimal('discount_amount', 15)->nullable();
            $table->decimal('sub_total', 15);
            $table->boolean('is_confirmed')->default(false);
            $table->string('discount_description', 255)->nullable();
            $table->boolean('is_finished')->default(1)->nullable();
            $table->string('token', 120)->nullable();
            $table->integer('payment_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->unsigned();
            $table->integer('qty');
            $table->decimal('price', 15);
            $table->decimal('tax_amount', 15);
            $table->text('options')->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->string('product_name');
            $table->float('weight')->default(0)->nullable();
            $table->integer('restock_quantity', false, true)->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('address');
            $table->integer('order_id')->unsigned();
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120)->nullable();
            $table->string('code', 20)->unique()->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('total_used')->unsigned()->default(0);
            $table->double('value')->nullable();
            $table->string('type', 60)->default('coupon')->nullable();
            $table->boolean('can_use_with_promotion')->default(false);
            $table->string('discount_on', 20)->nullable();
            $table->integer('product_quantity', false, true)->nullable();
            $table->string('type_option', 100)->default('amount');
            $table->string('target', 100)->default('all-orders');
            $table->decimal('min_order_price', 15)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wish_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cart', function (Blueprint $table) {
            $table->string('identifier');
            $table->string('instance');
            $table->longText('content');
            $table->nullableTimestamps();

            $table->primary(['identifier', 'instance']);
        });

        Schema::create('grouped_products', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_product_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('fixed_qty')->default(1);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar', 255)->nullable();
            $table->date('dob')->nullable();
            $table->string('phone', 25)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customer_password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email', 60)->nullable();
            $table->string('phone');
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('address');
            $table->integer('customer_id')->unsigned();
            $table->tinyInteger('is_default')->default(0)->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_related_relations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('from_product_id')->unsigned()->index();
            $table->integer('to_product_id')->unsigned()->index();
        });

        Schema::create('product_cross_sale_relations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('from_product_id')->unsigned()->index();
            $table->integer('to_product_id')->unsigned()->index();
        });

        Schema::create('product_up_sale_relations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('from_product_id')->unsigned()->index();
            $table->integer('to_product_id')->unsigned()->index();
        });

        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->integer('shipping_id')->unsigned();
            $table->enum('type', ['base_on_price', 'base_on_weight'])->default('base_on_price')->nullable();
            $table->integer('currency_id')->unsigned()->nullable();
            $table->decimal('from', 15)->default(0)->nullable();
            $table->decimal('to', 15)->default(0)->nullable();
            $table->decimal('price', 15)->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipping_rule_items', function (Blueprint $table) {
            $table->id();
            $table->integer('shipping_rule_id', false, true);
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->decimal('adjustment_price', 15)->default(0)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->string('action', 120);
            $table->string('description', 255);
            $table->integer('user_id', false, true)->nullable();
            $table->integer('order_id', false, true);
            $table->text('extras')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id', false, true);
            $table->integer('user_id', false, true)->nullable();
            $table->float('weight')->default(0)->nullable();
            $table->string('shipment_id', 120)->nullable();
            $table->string('note', 120)->nullable();
            $table->string('status', 120)->default('pending');
            $table->decimal('cod_amount', 15)->default(0)->nullable();
            $table->string('cod_status', 60)->default('pending');
            $table->string('cross_checking_status', 60)->default('pending');
            $table->decimal('price', 15)->default(0)->nullable();
            $table->integer('store_id', false, true)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('email', 60)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 255);
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('email', 60)->nullable();
            $table->string('phone', 20);
            $table->string('address', 255);
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->integer('company_id', false, true)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('email', 60)->nullable();
            $table->string('phone', 20);
            $table->string('address', 255);
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable(); 
            $table->integer('company_id', false, true)->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); 
        Schema::create('shipment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('action', 120);
            $table->string('description', 255);
            $table->integer('user_id', false, true)->nullable();
            $table->integer('shipment_id', false, true);
            $table->integer('order_id', false, true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('discount_products', function (Blueprint $table) {
            $table->integer('discount_id', false, true);
            $table->integer('product_id', false, true);
            $table->primary(['discount_id', 'product_id']);
        });

        Schema::create('discount_customers', function (Blueprint $table) {
            $table->integer('discount_id', false, true);
            $table->integer('customer_id', false, true);
            $table->primary(['discount_id', 'customer_id']);
        });

        Schema::create('discount_product_collections', function (Blueprint $table) {
            $table->integer('discount_id', false, true);
            $table->integer('product_collection_id', false, true);
            $table->primary(['discount_id', 'product_collection_id'], 'discount_product_collections_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('product_tag_product');
        Schema::dropIfExists('product_collection_products');
        Schema::dropIfExists('product_category_product');
        Schema::dropIfExists('prices');
        Schema::dropIfExists('products');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('product_collections');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('product_tag_product');
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('product_variation_items');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('product_with_attribute');
        Schema::dropIfExists('product_with_attribute_set');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_attribute_sets');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('shipping');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_product');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('wish_lists');
        Schema::dropIfExists('cart');
        Schema::dropIfExists('grouped_products');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_password_resets');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('product_up_sale_relations');
        Schema::dropIfExists('product_cross_sale_relations');
        Schema::dropIfExists('product_related_relations');
        Schema::dropIfExists('shipping_rules');
        Schema::dropIfExists('shipping_rule_items');
        Schema::dropIfExists('order_histories');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipment_histories');
        Schema::dropIfExists('store_locators');
        Schema::dropIfExists('discount_products');
        Schema::dropIfExists('discount_customers');
        Schema::dropIfExists('discount_product_collections');
    }
}
