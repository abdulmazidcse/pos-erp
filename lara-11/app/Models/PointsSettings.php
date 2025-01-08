<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PointsSettings
 * @package App\Models
 * @version May 8, 2022, 7:01 am UTC
 *
 * @property integer $enable_points_rewards
 * @property integer $enable_signup_points
 * @property integer $signup_points
 * @property integer $enable_referral_points
 * @property integer $referral_points
 * @property integer $enable_social_point
 * @property integer $social_share_facebook
 * @property integer $social_share_twitter
 * @property integer $custom_points_on_cart
 * @property integer $cart_points_rate
 * @property integer $cart_price_rate
 * @property integer $enable_points_order_total
 */
class PointsSettings extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'points_settings';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'id',
        'enable_points_rewards',
        'enable_signup_points',
        'signup_points',
        'enable_referral_points',
        'referral_points',
        'enable_social_point',
        'social_share_points',
        'social_share_facebook',
        'social_share_twitter',
        'custom_points_on_cart',
        'cart_points_rate',
        'cart_price_rate',
        'enable_points_order_total',
        'points_within_order_range',        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'enable_points_rewards' => 'integer',
        'enable_signup_points' => 'integer',
        'enable_referral_points' => 'integer',
        'referral_points' => 'integer',
        'enable_social_point' => 'integer',
        'social_share_facebook' => 'integer',
        'social_share_points' => 'integer',
        'social_share_twitter' => 'integer',
        'custom_points_on_cart' => 'integer',
        'cart_points_rate' => 'integer',
        'cart_price_rate' => 'integer',
        'enable_points_order_total' => 'integer',
        'points_within_order_range' => 'json'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
