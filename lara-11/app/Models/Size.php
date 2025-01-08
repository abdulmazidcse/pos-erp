<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Size
 * @package App\Models
 * @version March 7, 2022, 5:26 am UTC
 *
 * @property string $name
 * @property string $code
 */
class Size extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'sizes';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'value',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];

    public function colors(){
        return $this->belongsToMany( Size::class, 'products_sizes', 'product_id', 'size_id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
    
}
