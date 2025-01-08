<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class HoldSaleDiscount
 * @package App\Models
 * @version June 7, 2022, 5:36 pm UTC
 *
 * @property integer $hold_sale_id
 * @property json $discount_value
 */
class HoldSaleDiscount extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'hold_sale_discounts';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'hold_sale_id',
        'discount_value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'hold_sale_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
