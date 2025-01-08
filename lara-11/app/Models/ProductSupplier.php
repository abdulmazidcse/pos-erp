<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductSupplier
 * @package App\Models
 * @version March 6, 2022, 6:06 am UTC
 *
 * @property string $name
 */
class ProductSupplier extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'product_suppliers';
    

    protected $dates = ['deleted_at'];



    public $fillable = ['supplier_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */ 
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    } 
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    } 

    
}
