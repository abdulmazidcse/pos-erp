<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
/**
 * Class Taxes
 * @package App\Models
 * @version April 4, 2022, 9:59 am UTC
 *
 * @property string $title
 */
class Taxes extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'taxes';
    

    protected $dates = ['deleted_at'];
 

    public $fillable = [
        'title','value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string'
    ];

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

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
