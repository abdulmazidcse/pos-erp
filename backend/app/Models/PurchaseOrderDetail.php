<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
    use SoftDeletes;

    use HasFactory;

    protected $guarded = [];


    //Relations
    public function purchase_orders()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }
}
