<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_no',
        'transfer_type',
        'from_warehouse_id',
        'to_warehouse_id',
        'from_outlet_id',
        'to_outlet_id',
        'total_item',
        'total_quantity',
        'total_cost',
        'shipping_cost',
        'grand_total',
        'documents',
        'note'
    ];


    public function stock_transfer_details()
    {
        return $this->hasMany(StockTransferDetail::class, 'stock_transfer_id', 'id');
    }

    public function from_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id', 'id');
    }

    public function to_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id', 'id');
    }

    public function from_outlet()
    {
        return $this->belongsTo(Outlet::class, 'from_outlet_id', 'id');
    }

    public function to_outlet()
    {
        return $this->belongsTo(Outlet::class, 'to_outlet_id', 'id');
    }

}
