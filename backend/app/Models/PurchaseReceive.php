<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PurchaseReceive
 * @package App\Models
 * @version April 3, 2022, 8:57 am UTC
 *
 * @property varchar $reference_no
 */
class PurchaseReceive extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'purchase_receives';
    

    protected $dates = ['deleted_at'];



    protected $guarded = [];
//    public $fillable = [
//        'reference_no'
//    ];

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
    public static $rules = [
        
    ];

    // Relation
    public function receive_products()
    {
        return $this->hasMany(OrderRequisitionDetail::class, 'purchase_receive_id', 'id');
    }

    public function purchase_receive_details()
    {
        return $this->hasMany(PurchaseReceiveDetail::class, 'purchase_receive_id', 'id');

    }

    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }


    public function warehouses()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function sequences()
    {
        return $this->hasMany(ProductSequence::class, 'purchase_receive_id', 'id');
    }

    
}
