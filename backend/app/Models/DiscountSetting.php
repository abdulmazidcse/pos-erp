<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DiscountSetting
 * @package App\Models
 * @version May 18, 2022, 10:01 am UTC
 *
 * @property integer $customer_wise
 * @property integer $customer_group_wise
 * @property integer $individual_customer
 * @property integer $product_wise
 * @property integer $category_wise
 * @property integer $sub_category_wise
 * @property integer $vendor_wise
 * @property integer $slow_moving_product
 * @property integer $slow_moving_product_discount
 * @property integer $fast_moving_product
 * @property integer $fast_moving_product_discount
 * @property integer $sales_platform
 * @property integer $sales_platform_pos
 * @property integer $sales_platform_pos_discount
 * @property integer $sales_platform_ecom
 * @property integer $sales_platform_ecom_discount
 * @property integer $gp_wise
 * @property integer $gp_wise_discount
 * @property integer $enable_conditional_discount
 * @property json $discount_within_range
 */
class DiscountSetting extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'discount_settings';    

    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'customer_wise',
        'customer_group_wise',
        'individual_customer',
        'product_wise',
        'category_wise',
        'sub_category_wise',
        'vendor_wise',
        'slow_moving_product',
        'slow_moving_product_discount',
        'fast_moving_product',
        'fast_moving_product_discount',
        'sales_platform',
        'sales_platform_pos',
        'sales_platform_pos_discount',
        'sales_platform_ecom',
        'sales_platform_ecom_discount',
        'gp_wise',
        'gp_wise_discount',
        'enable_conditional_discount',
        'discount_within_range',
        
        'customer_offer_within_range',
        'product_offer_within_range',
        'category_offer_within_range',
        'sub_cat_offer_within_range',
        'vendor_offer_within_range',
        'slow_moving_offer_within_range',
        'fast_moving_offer_within_range',
        'sales_platform_pos_offer_within_range',
        'sales_platform_ecom_offer_within_range',
        'gp_offer_within_range',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_wise' => 'integer',
        'customer_group_wise' => 'integer',
        'individual_customer' => 'integer',
        'product_wise' => 'integer',
        'category_wise' => 'integer',
        'sub_category_wise' => 'integer',
        'vendor_wise' => 'integer',
        'slow_moving_product' => 'integer',
        'slow_moving_product_discount' => 'integer',
        'fast_moving_product' => 'integer',
        'fast_moving_product_discount' => 'integer',
        'sales_platform' => 'integer',
        'sales_platform_pos' => 'integer',
        'sales_platform_pos_discount' => 'integer',
        'sales_platform_ecom' => 'integer',
        'sales_platform_ecom_discount' => 'integer',
        'gp_wise' => 'integer',
        'gp_wise_discount' => 'integer',
        'enable_conditional_discount' => 'integer',
        'discount_within_range' =>'json',
        'customer_offer_within_range' => 'json',
        'product_offer_within_range' => 'json',
        'category_offer_within_range' => 'json',
        'sub_cat_offer_within_range' => 'json',
        'vendor_offer_within_range' => 'json',
        'slow_moving_offer_within_range' => 'json',
        'fast_moving_offer_within_range' => 'json',
        'sales_platform_pos_offer_within_range' => 'json',
        'sales_platform_ecom_offer_within_range' => 'json',
        'gp_offer_within_range' => 'json',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
