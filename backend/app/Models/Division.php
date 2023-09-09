<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Division
 * @package App\Models
 * @version February 16, 2022, 10:06 am UTC
 *
 * @property string $name
 */
class Division extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'divisions';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name','bn_name','status'
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

    
}
