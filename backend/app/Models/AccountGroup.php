<?php

namespace App\Models;

use App\Traits\ChartOfAccountsTrait;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AccountGroup
 * @package App\Models
 * @version July 20, 2022, 6:13 am UTC
 *
 * @property int $id
 */
class AccountGroup extends Model
{
    use SoftDeletes;

    use HasFactory;

    use ChartOfAccountsTrait;

    public $table = 'account_groups';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'group_code',
        'parent_id',
        'group_name',
        'order',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // Releations
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function childrenGroups()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(AccountLedger::class, 'group_id', 'id');
    }

    
}
