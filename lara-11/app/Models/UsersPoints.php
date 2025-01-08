<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UsersPoints
 * @package App\Models
 * @version May 9, 2022, 7:36 am UTC
 *
 * @property integer $user_id
 * @property integer $sale_id
 * @property string $type
 * @property integer $points
 */
class UsersPoints extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'users_points';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'user_id',
        'sale_id',
        'type',
        'points'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'sale_id' => 'integer',
        'type' => 'string',
//        'points' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ]; 
    public function customers()
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }
}
