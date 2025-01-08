<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Coupon
 * @package App\Models
 * @version April 26, 2022, 3:57 am UTC
 *
 * @property string $code
 * @property string $name
 * @property string $description
 * @property integer $uses
 * @property integer $max_uses
 * @property integer $max_uses_user
 * @property tinyInteger $type
 * @property integer $discount_amount
 * @property boolean $is_fixed
 * @property string $starts_at
 * @property string $expires_at
 */
class Coupon extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'coupons';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'code',
        'name',
        'description',
        'uses',
        'max_uses',
        'max_uses_user',
        'status',
        'discount_amount',
        'is_fixed',
        'start_at',
        'expires_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'name' => 'string',
        'description' => 'string',
        'uses' => 'integer',
        'max_uses' => 'integer',
        'max_uses_user' => 'integer',
        'is_fixed' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
    
}
