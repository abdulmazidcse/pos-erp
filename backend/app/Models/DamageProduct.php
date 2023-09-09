<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageProduct extends Model
{
    use HasFactory;

    protected $table    = "damage_products";


    protected $fillable = [
        'outlet_id',
        'warehouse_id',
        'product_id',
        'product_stock_id',
        'product_unit_id',
        'expires_date',
        'damage_quantity',
        'damage_weight',
        'cost_price',
        'notes'
    ];


    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
