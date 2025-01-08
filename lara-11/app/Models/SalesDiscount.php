<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SalesDiscount
 * @package App\Models
 * @version June 7, 2022, 5:12 pm UTC
 *
 * @property integer $sale_id
 * @property json $discount_value
 */
class SalesDiscount extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'sales_discounts';
    

    protected $dates = ['deleted_at']; 


    public $fillable = [  
        'sale_item_id',
        'key', 
        'value'
    ];

    //protected $guarded = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'sale_item_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function saleItems()
    {
        return $this->belongsTo(SaleItem::class, 'sale_item_id', 'id');
    }

    
}
