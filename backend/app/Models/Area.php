<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Area
 * @package App\Models
 * @version February 16, 2022, 10:08 am UTC
 *
 * @property string $name
 */
class Area extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'areas';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'district_id','name','bn_name','status'
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


    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    
}
