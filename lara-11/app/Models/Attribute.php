<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Attribute
 * @package App\Models
 * @version April 13, 2022, 9:27 am UTC
 *
 * @property string $title
 * @property int $status
 */
class Attribute extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'attributes';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'title',
        'status'
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

    
}
