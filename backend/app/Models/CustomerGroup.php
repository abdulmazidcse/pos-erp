<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerGroup
 * @package App\Models
 * @version April 7, 2022, 8:54 am UTC
 *
 * @property string $title
 * @property string $value
 */
class CustomerGroup extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'customer_groups';
    

    protected $dates = ['deleted_at'];



    public $fillable = [ 'title', 'value'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [ ];


    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_group_id', 'id');
    }

    
}
