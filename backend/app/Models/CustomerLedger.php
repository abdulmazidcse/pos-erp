<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerLedger
 * @package App\Models
 * @version May 13, 2022, 7:43 pm UTC
 *
 * @property unsignedBigInteger $customer_id
 * @property string $transaction_type
 * @property number $opening_balance
 * @property number $collection_amount
 * @property number $return_amount
 * @property number $discount_amount
 * @property number $closing_balance
 * @property string $transaction_date
 */
class CustomerLedger extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'customer_ledgers';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'customer_id',
        'company_id',
        'sale_id',
        'transaction_type',
        'note',
        'opening_balance',
        'sales_amount',
        'payment_receive_amount',
        'closing_balance',
        'transaction_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_type' => 'string',
        'opening_balance' => 'double',   
        'closing_balance' => 'double', 
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
