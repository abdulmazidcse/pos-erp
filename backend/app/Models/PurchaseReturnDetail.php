<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded = [];


    //Relations
    public function purchase_returns()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'product_unit_id', 'id');
    }
}
