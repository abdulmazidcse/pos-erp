<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AccountType
 * @package App\Models
 * @version July 24, 2022, 4:49 am UTC
 *
 * @property string $type_name
 */
class AccountType extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'account_types';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'type_code',
        'type_name',
        'class_id',
        'parent_type_id',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type_name' => 'string',
        'status'    => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    //Relations
    public function account_classes()
    {
        return $this->belongsTo(AccountClass::class, 'class_id', 'id');
    }

    public function type_parents()
    {
        return $this->hasOne(self::class, 'id', 'parent_type_id');
    }

    public function type_children()
    {
        return $this->hasMany(self::class, 'parent_type_id', 'id');
    }

    public function account_ledgers()
    {
        return $this->hasMany(AccountLedger::class, 'detail_type_id', 'id');
    }

    
}
