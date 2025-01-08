<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReceiveDetail extends Model
{
    use SoftDeletes;

    use HasFactory;


    protected $guarded = [];


    // Relations
    public function purchase_receive()
    {
        return $this->belongsTo(PurchaseReceive::class, 'purchase_receive_id', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'receive_product_id', 'id');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'receive_supplier_id', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'receive_product_unit_id', 'id');
    }

}
