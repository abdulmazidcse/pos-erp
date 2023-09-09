<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AccountVoucher
 * @package App\Models
 * @version August 4, 2022, 11:13 am UTC
 *
 * @property string $code
 */
class AccountVoucher extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'account_vouchers';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'vcode',
        'invoice_reference',
        'invoice_type',
        'cost_center_id',
        'vnumber',
        'vtype_id',
        'vtype_value',
        'payment_type',
        'cheque_no',
        'cheque_date',
        'fiscal_year_id',
        'vdate',
        'global_note',
        'entry_level',
        'modified_item'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'vcode' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    //Relations
    public function account_voucher_transactions()
    {
        return $this->hasMany(AccountVoucherTransaction::class, 'voucher_id', 'id');
    }

    public function entry_types()
    {
        return $this->belongsTo(EntryType::class, 'vtype_id', 'id');
    }


    public function purchase_receives()
    {
        return $this->belongsTo(PurchaseReceive::class, 'invoice_reference', 'id');
    }
    
}
