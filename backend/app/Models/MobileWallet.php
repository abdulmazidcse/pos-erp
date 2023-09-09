<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MobileWallet
 * @package App\Models
 * @version April 11, 2022, 7:50 am UTC
 *
 * @property string $name
 * @property string $mobile_wallet
 * @property string $agent_name
 * @property number $mobile_number
 */
class MobileWallet extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'mobile_wallets';
    

    protected $dates = ['deleted_at'];



    public $fillable = [ 
        'mobile_wallet',
        'agent_name',
        'mobile_number',
        'company_id',
        'charge_percent',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [ 
        'mobile_wallet' => 'string',
        'agent_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    
}
