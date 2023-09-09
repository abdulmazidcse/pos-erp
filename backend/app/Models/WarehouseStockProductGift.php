<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseStockProductGift extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'gift_name',
        'stock_quantity'
    ];
}
