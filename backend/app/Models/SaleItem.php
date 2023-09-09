<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SaleItem
 * @package App\Models
 * @version April 21, 2022, 8:05 am UTC
 *
 * @property integer $sale_id
 * @property integer $product_id
 * @property number $quantity
 * @property number $discount
 * @property number $item_quantity
 * @property number $vat
 * @property integer $vat_id
 * @property number $inite_price
 * @property number $net_unit_price
 * @property number $cost_price
 */
class SaleItem extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'sale_items';
    

    protected $dates = ['deleted_at']; 

    public $fillable = [
        'sale_id',
        'stock_id',
        'product_id',
        'quantity',
        'discount', 
        'vat',
        'vat_id', 
        'mrp_price',
        'cost_price',
        'return_type',
        'uom',
        'weight'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'sale_id' => 'integer',
        'product_id' => 'integer',
//        'quantity' => 'decimal:2',
//        'discount' => 'decimal:2',
//        'vat' => 'decimal:2',
        'vat_id' => 'integer', 
//        'mrp_price' => 'decimal:2',
//        'cost_price' => 'decimal:2'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function sales()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function salesDiscount()
    {
        return $this->hasMany(SalesDiscount::class, 'sale_item_id', 'id');
    }

    // public function getBestSellingProducts($limit = 10)
    // {
    //    $bestSellingProducts = TransactionSellLine::select(
    //             'item_id',
    //             'items.name as item_name',
    //             'items.sku as item_sku',
    //             DB::raw('SUM((quantity * unit_price_inc_tax) - discount_amount - item_tax) as total_sale')
    //    )
    //    ->leftJoin('items', 'items.id', '=', 'transaction_sell_lines.item_id')
    //    ->groupBy('item_id')
    //    ->orderBy('total_sale', 'desc')
    //    ->limit($limit)
    //    ->get();

    //    return $bestSellingProducts;
    // }

    public function getCreatedAtAttribute($date)
    {
        return date('d M Y',strtotime($date));
        //return Carbon::parse($date)->format('d M Y');
    }


}
