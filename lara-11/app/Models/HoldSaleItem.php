<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class HoldSaleItem
 * @package App\Models
 * @version June 7, 2022, 5:39 pm UTC
 *
 * @property integer $sale_id
 */
class HoldSaleItem extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'hold_sale_items';
    

    protected $dates = ['deleted_at'];
 

    public $fillable = [
        'hold_sale_id', 
        'product_id', 
        'product_stock_id',
        'quantity', 
        'discount', 
        'vat', 
        'vat_id', 
        'mrp_price', 
        'cost_price',
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
    public static $rules = [];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    } 

    
}
