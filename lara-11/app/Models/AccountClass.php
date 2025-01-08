<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AccountClass
 * @package App\Models
 * @version August 2, 2022, 8:58 am UTC
 *
 * @property string $name
 */
class AccountClass extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'account_classes';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'code',
        'company_id',
        'is_credit_positive'
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

    // Relations
    public function test()
    {

    }

    
}
