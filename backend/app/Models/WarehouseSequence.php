<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'outlet_id', 'warehouse_id', 'warehouse_stock_product_id', 'sales_id', 'colors_id', 'sizes_id' ,'sequence' , 'expiry_date', 'quantity',  'weight',  'sale_price' , 'purchases_price',  'status'
    ];
    public $timestamps = true;
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function getNextSequence()
    {
        $nextNumber = $this->current_number + 1;
        $formattedNumber = str_pad($nextNumber, $this->digit, '0', STR_PAD_LEFT);
        return $this->prefix . $formattedNumber . $this->suffix;
    }
    public function incrementSequence()
    {
        $this->current_number += 1;
        $this->save();
    }

}
