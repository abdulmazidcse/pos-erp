<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PurchaseOrder
 * @package App\Models
 * @version March 2, 2022, 4:07 am UTC
 *
 * @property string $reference_no
 */
class PurchaseOrder extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'purchase_orders';
    

    protected $dates = ['deleted_at'];


    protected $guarded  = [];
//    public $fillable = [
//        'reference_no'
//    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'reference_no' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // Relations
    public function purchase_order_details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id', 'id');
    }

    public function purchase_products()
    {
        return $this->hasMany(OrderRequisitionDetail::class, 'purchase_order_id', 'id');
    }

    public function store_requisition()
    {
        return $this->belongsTo(StoreRequisition::class, 'store_requisition_id', 'id');
    }

    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'delivery_to_outlet', 'id')->with('company');
    }

    public function warehouses()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id')->with('company');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    
}
