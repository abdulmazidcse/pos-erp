<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AttributeValue
 * @package App\Models
 * @version April 13, 2022, 9:28 am UTC
 *
 * @property int $attrbute_id
 * @property string $value
 */
class AttributeValue extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'attribute_values';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'attrbute_id',
        'value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [ ];

    
}
