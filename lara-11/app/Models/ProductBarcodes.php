<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductBarcodes
 * @package App\Models
 * @version March 2, 2022, 1:15 pm UTC
 *
 * @property string $name
 */
class ProductBarcodes extends Model
{
    //use SoftDeletes;

    use HasFactory;

    public $table = 'product_barcodes';
    

    //protected $dates = ['deleted_at'];


    protected $guarded = [];

    // public $fillable = [
    //     'code'
    // ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];
    public $timestamps = false;


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = ['code'  => 'required|unique:product_barcodes,code',];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    
}
