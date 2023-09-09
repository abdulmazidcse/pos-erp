<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements( 'id' );
    
            // The voucher code
            $table->string( 'code' )->nullable( );

            // The human readable voucher code name
            $table->string( 'name' );

            // The description of the voucher - Not necessary 
            $table->text( 'description' )->nullable( );

            // The number of uses currently
            $table->integer( 'uses' )->unsigned( )->nullable( );

            // The max uses this voucher has
            $table->integer( 'max_uses' )->unsigned()->nullable( );

            // How many times a user can use this voucher.
            $table->integer( 'max_uses_user' )->unsigned( )->nullable( ); 

            // active and inactive 
            $table->tinyInteger( 'status' )->unsigned( );

            // The amount to discount by in this example.
            $table->integer( 'discount_amount' );

            // Whether or not the voucher is a percentage or a fixed price. 
            $table->boolean( 'is_fixed' )->default( true );
            
            // When the voucher begins
            $table->date( 'start_at' )->nullable( );

            // When the voucher ends
            $table->date( 'expires_at' )->nullable( );

            // You know what this is...
            $table->timestamps( );

            // We like to horde data.
            $table->softDeletes( );
        });
        Schema::create( 'user_coupon', function ( Blueprint $table ) {
            $table->integer( 'user_id' )->unsigned( );
            $table->bigInteger( 'voucher_id' )->unsigned( );
            $table->unique( [ 'user_id', 'voucher_id' ] );
        });
        Schema::create( 'product_coupon', function ( Blueprint $table ) {
            $table->integer( 'product_id' )->unsigned( );
            $table->bigInteger( 'voucher_id' )->unsigned( );
            $table->unique( [ 'product_id', 'voucher_id' ] );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('user_coupon');
        Schema::dropIfExists('product_coupon'); 
    }
}
