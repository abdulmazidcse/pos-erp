<?php

namespace App\Models;

use App\Traits\ChartOfAccountsTrait;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AccountLedger
 * @package App\Models
 * @version July 21, 2022, 6:34 am UTC
 *
 * @property int $ledger_code
 */
class AccountLedger extends Model
{
    use SoftDeletes;

    use HasFactory;

    use ChartOfAccountsTrait;

    public $table = 'account_ledgers';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'ledger_code',
        'ledger_name',
        'parent_id',
        'company_id',
        'type_id',
        'detail_type_id',
        'ledger_type',
        'opening_balance',
        'balance_date',
        'is_control_transaction',
        'is_master_head',
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


    // Relations
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children_accounts()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function account_types()
    {
        return $this->belongsTo(AccountType::class, 'detail_type_id', 'id');
    }

    public function account_transactions()
    {
        return $this->hasMany(AccountVoucherTransaction::class, 'ledger_id', 'id');
    }


    public function supplier_payable()
    {
        return $this->hasOne(Supplier::class, 'payable_ledger_id', 'id');
    }


    public function customer_receivable()
    {
        return $this->hasOne(Customer::class, 'receivable_ledger_id', 'id');
    }

    
}
