<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DiscountTitle
 * @package App\Models
 * @version May 16, 2022, 7:14 am UTC
 *
 * @property string $title
 * @property integer $order_by
 */
class DiscountTitle extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'discount_titles';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'title',
        'order_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'order_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required'
    ];

    
}
