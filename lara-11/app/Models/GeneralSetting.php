<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class GeneralSetting
 * @package App\Models
 * @version October 26, 2022, 3:16 am UTC
 *
 * @property tinyinteger $payment_status
 * @property tinyinteger $date_status
 * @property string $date_format
 * @property string $sender_id
 */
class GeneralSetting extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'general_settings';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'invoice_sms_status',
        'payment_status',
        'date_status',
        'date_format',
        'sender_id',
        'api_key',
        'sms_text',
        'purchase_key',
        'ip_address'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_format' => 'string',
        'sender_id' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    
}
