<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Unit
 * @package App\Models
 * @version February 10, 2022, 5:10 am UTC
 *
 * @property string $unit_code
 */
class Unit extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'units';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'unit_code',
        'unit_name',
        'base_unit',
        'operator',
        'operation_value',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'unit_code' => 'string',
        'unit_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit', 'id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    
}
