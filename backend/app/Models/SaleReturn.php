<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SaleReturn
 * @package App\Models
 * @version July 16, 2022, 11:26 am UTC
 *
 * @property integer $sale_id
 * @property string $return_type
 * @property number $return_amount
 * @property string $return_reason
 */
class SaleReturn extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'sale_returns';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'sale_id',
        'voucher_id',
        'sale_return_id',
        'return_type',
        'return_amount',
        'return_reason'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'sale_id' => 'integer',
        'return_type' => 'string',
//        'return_amount' => 'double',
        'return_reason' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function saleReturnItems()
    {
        return $this->hasMany(SaleReturnItem::class, 'sale_return_id', 'id')
        ->with(['return_products', 'replace_products', 'sale_items']);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'sale_id')
            ->where('return_type','!=','0')
            ->with('products');
    }

    public function sale(){
        return $this->belongsTo(Sale::class); 
    } 

    public function getCreatedAtAttribute($date)
    { 
        return date('d M Y',strtotime($date));  
    }

}
