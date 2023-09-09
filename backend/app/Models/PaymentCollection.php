<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PaymentCollection
 * @package App\Models
 * @version April 21, 2022, 8:16 am UTC
 *
 * @property integer $sale_id
 * @property string $paying_by
 * @property number $amount
 * @property string $payment_note
 */
class PaymentCollection extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'payment_collections'; 

    protected $dates = ['deleted_at'];


    //protected $guarded = [];
    public $fillable = [
        'sale_id',
        'paying_by',
        'amount',
        'wallet_id',
        'card_reference_no',
        'transaction_no',
        'bank_id',
        'payment_note'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'sale_id' => 'integer',
        'paying_by' => 'string',
        'payment_note' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function sales()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function banks()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id', 'id');
    }

    public function mobile_wallets()
    {
        return $this->belongsTo(MobileWallet::class, 'wallet_id', 'id');
    }
    public function getCreatedAtAttribute($date)
    { 
        return date('d M Y',strtotime($date));  
    }
}
