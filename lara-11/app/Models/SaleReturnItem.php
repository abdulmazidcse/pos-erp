<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SaleReturnItem
 * @package App\Models
 * @version July 16, 2022, 11:43 am UTC
 *
 * @property integer $sale_id
 * @property integer $sale_item_id
 * @property integer $return_type
 */
class SaleReturnItem extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'sale_return_items';
    

    protected $dates = ['deleted_at'];



    public $fillable = [ 
        'sale_id',
        'sale_item_id',
        'item_pro_id',
        'replace_pro_id',
        'sale_item_qty',
        'sale_r_qty',
        'return_type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'sale_id' => 'integer',
    //     'sale_item_id' => 'integer',
    //     'return_type' => 'integer'
    // ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];


    public function return_products()
    {
        return $this->belongsTo(Product::class, 'item_pro_id', 'id');
    }

    // Replace Products
    public function replace_products()
    {
        return $this->belongsTo(Product::class, 'replace_pro_id', 'id');
    }

    public function sale_items()
    {
        return $this->belongsTo(SaleItem::class, 'sale_item_id', 'id');
    }

    
}
