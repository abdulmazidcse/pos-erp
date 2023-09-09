<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRequisitionDetail extends Model
{
    use SoftDeletes;

    use HasFactory;

    protected $guarded = [];


    public function purchase_orders()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function store_requisitions()
    {
        return $this->belongsTo(StoreRequisition::class, 'purchase_requisition_id', 'id');
    }

    public function purchase_receives()
    {
        return $this->belongsTo(PurchaseReceive::class, 'purchase_receive_id', 'id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


}
