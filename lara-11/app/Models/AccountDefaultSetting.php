<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDefaultSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'supplier_payable_account_type',
        'supplier_discount_account_type',
        'supplier_advance_payment_account_type',
        'bank_account_asset_type',
        'bank_loan_account_liability_type',
        'bank_charge_account_expense_type',
        'bank_loan_interest_expense_type',
        'bank_interest_income_type',
        'customer_receivable_account_type',

        'inventory_account', // Inventory Transaction
        'cogs_account',
        'inventory_damage_account',
        'inventory_adjustment_account',
        'petty_cash_account', // cash transaction
        'cash_in_hand_account',
        'bank_account', // Bank Transaction
        'bank_loan_account',
        'bank_loan_interest_expense_account',
        'bank_charge_expense_account',
        'bank_interest_income_account',
        'trade_payable_account', // Purchase
        'supplier_discount_account',
        'cash_sales_account', // sales
        'mfs_sales_account',
        'bkash_sales_account',
        'rocket_sales_account',
        'nagad_sales_account',
        'card_sales_account',
        'credit_sales_account',
        'account_receivable_ledger',
        'customer_discount_account',
        'bank_reference_ledger_mfs',    // Reference Ledger
        'mfs_charge_ledger',
        'bank_reference_ledger_bkash',
        'bkash_charge_ledger',
        'bank_reference_ledger_rocket',
        'rocket_charge_ledger',
        'bank_reference_ledger_nagad',
        'nagad_charge_ledger',
    ];


}
