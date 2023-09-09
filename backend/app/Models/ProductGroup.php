<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductGroup
 * @package App\Models
 * @version April 13, 2022, 9:31 am UTC
 *
 * @property int $product_id
 * @property int $group_id
 * @property int $attribute_id
 * @property int $attribute_value_id
 */
class ProductGroup extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'product_groups';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'product_id',
        'group_id',
        'attribute_id',
        'attribute_value_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    
}
