<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProductsLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'category_id',
        'outlet_id',
        'in_stock_quantity',
        'in_stock_weight',
        'stock_quantity',
        'stock_weight',
        'out_stock_quantity',
        'out_stock_weight',
        'expires_date',
        'stock_type',
        'note',
        'user_id',
    ];

    public $dates = ['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function product_categories()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

}
