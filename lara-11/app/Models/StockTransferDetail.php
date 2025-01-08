<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransferDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'stock_transfer_id',
        'stock_id',
        'product_id',
        'quantity',
        'weight',
        'unit_id',
        'net_unit_cost',
        'total_amount',

    ];

    public $dates = ['deleted_at'];


    public function stock_transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
