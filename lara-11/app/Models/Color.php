<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Color
 * @package App\Models
 * @version March 7, 2022, 5:28 am UTC
 *
 * @property string $code
 * @property string $name
 */
class Color extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'colors';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'code',
        'name',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required',
        'name' => 'required'
    ];

    public function products(){
        return $this->belongsToMany( Product::class, 'products_colors', 'color_id', 'product_id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    // public function product()
    // {
    //     return $this->belongsToMany('App\Model\Product','products');
    // }

    
}
