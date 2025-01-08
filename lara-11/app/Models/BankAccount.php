<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Account
 * @package App\Models
 * @version May 16, 2022, 7:07 am UTC
 *
 * @property int $account_no
 */
class BankAccount extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'bank_accounts';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'account_no',
        'bank_name',
        'initial_balance',
        'current_balance',
        'company_id',
        'charge_percent',
        'note',
        'is_default',
        'status',

        'bank_asset_account',
        'bank_loan_account',
        'bank_charge_account',
        'bank_interest_expense_account',
        'bank_interest_income_account',
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


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function bank_asset_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'bank_asset_account', 'id');
    }

    public function bank_loan_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'bank_loan_account', 'id');
    }

    public function bank_charge_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'bank_charge_account', 'id');
    }

    public function bank_interest_expense_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'bank_interest_expense_account', 'id');
    }

    public function bank_interest_income_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'bank_interest_income_account', 'id');
    }

    
}
