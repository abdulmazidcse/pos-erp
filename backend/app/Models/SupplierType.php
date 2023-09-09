<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SupplierType
 * @package App\Models
 * @version March 12, 2022, 2:56 am UTC
 *
 * @property string $name
 */
class SupplierType extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'supplier_types';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name', 'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'type_id', 'id');
    }

    
}
