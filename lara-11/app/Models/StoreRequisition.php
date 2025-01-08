<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class StoreRequisition
 * @package App\Models
 * @version March 14, 2022, 3:57 am UTC
 *
 * @property string $requisition_no
 */
class StoreRequisition extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'store_requisitions';
    

    protected $dates = ['deleted_at'];



    protected $guarded  = [];
//    public $fillable = [
//        'requisition_no'
//    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'requisition_no' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // Relations
    public function store_requisition_details()
    {
        return $this->hasMany(StoreRequisitionDetail::class, 'store_requisition_id', 'id');
    }

    public function purchase_requisition()
    {
        return $this->hasMany(OrderRequisitionDetail::class, 'purchase_requisition_id', 'id');
    }

    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }



    
}
