<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductImages
 * @package App\Models
 * @version March 2, 2022, 1:13 pm UTC
 *
 * @property string $name
 */
class ProductImages extends Model
{
    //use SoftDeletes;

    use HasFactory;

    public $table = 'product_images';
    

    //protected $dates = ['deleted_at'];

    protected $guarded = [];

    // public $fillable = [
    //     'name'
    // ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    
}
