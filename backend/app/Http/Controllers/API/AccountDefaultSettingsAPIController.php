<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountDefaultSettingsAPIController extends AppBaseController
{

    public function getAccountDefaultSetting(Request $request)
    {
        $company_id = checkCompanyId($request); 
        $account_default_setting = AccountDefaultSetting::when($company_id, function($q) use($company_id){
            return $q->where('company_id', $company_id);
        })->first();

        if(empty($account_default_setting)) {
            return $this->sendError('Account Default Setting Data Not Found!');
        }

        // return $account_default_setting;

        $return_data    = [
            'company_id'    => $company_id,
            'supplier_payable_account_type' => $this->getTypeData($account_default_setting->supplier_payable_account_type),
            'supplier_discount_account_type' => $this->getTypeData($account_default_setting->supplier_discount_account_type),
            'supplier_advance_payment_account_type' => $this->getTypeData($account_default_setting->supplier_advance_payment_account_type),
            'bank_account_asset_type' => $this->getTypeData($account_default_setting->bank_account_asset_type),
            'bank_loan_account_liability_type' => $this->getTypeData($account_default_setting->bank_loan_account_liability_type),
            'bank_charge_account_expense_type' => $this->getTypeData($account_default_setting->bank_charge_account_expense_type),
            'bank_loan_interest_expense_type' => $this->getTypeData($account_default_setting->bank_loan_interest_expense_type),
            'bank_interest_income_type' => $this->getTypeData($account_default_setting->bank_interest_income_type),
            'customer_receivable_account_type' => $this->getTypeData($account_default_setting->customer_receivable_account_type),

            'inventory_ledger_id' => $this->getLedgerData($account_default_setting->inventory_account),
            'cogs_ledger_id' => $this->getLedgerData($account_default_setting->cogs_account),
            'stock_damage_ledger_id' => $this->getLedgerData($account_default_setting->inventory_damage_account),
            'inv_adj_ledger_id' => $this->getLedgerData($account_default_setting->inventory_adjustment_account),
            'petty_cash_ledger_id' => $this->getLedgerData($account_default_setting->petty_cash_account),
            'cash_hand_ledger_id' => $this->getLedgerData($account_default_setting->cash_in_hand_account),
            'bank_ledger_id' =>  $this->getLedgerData($account_default_setting->bank_account),
            'bank_loan_ledger_id' =>  $this->getLedgerData($account_default_setting->bank_loan_account),
            'bank_loan_interest_expense_ledger_id' =>  $this->getLedgerData($account_default_setting->bank_loan_interest_expense_account),
            'bank_charge_expense_ledger_id' =>  $this->getLedgerData($account_default_setting->bank_charge_expense_account),
            'bank_interest_income_ledger_id' =>  $this->getLedgerData($account_default_setting->bank_interest_income_account),
            'trade_payable_ledger_id' =>  $this->getLedgerData($account_default_setting->trade_payable_account),
            'supplier_discount_ledger_id' =>  $this->getLedgerData($account_default_setting->supplier_discount_account),
            'cash_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->cash_sales_account),
            'mfs_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->mfs_sales_account),
            'bkash_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->bkash_sales_account),
            'rocket_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->rocket_sales_account),
            'nagad_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->nagad_sales_account),
            'card_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->card_sales_account),
            'credit_sales_ledger_id' =>  $this->getLedgerData($account_default_setting->credit_sales_account),
            'account_receiveable_ledger_id' =>  $this->getLedgerData($account_default_setting->account_receivable_ledger),
            'customer_discount_ledger_id' =>  $this->getLedgerData($account_default_setting->customer_discount_account),
            'bank_reference_ledger_mfs' =>  $this->getLedgerData($account_default_setting->bank_reference_ledger_mfs),
            'mfs_charge_ledger_id' =>  $this->getLedgerData($account_default_setting->mfs_charge_ledger),
            'bank_reference_ledger_bkash' =>  $this->getLedgerData($account_default_setting->bank_reference_ledger_bkash),
            'bkash_charge_ledger_id' =>  $this->getLedgerData($account_default_setting->bkash_charge_ledger),
            'bank_reference_ledger_rocket' =>  $this->getLedgerData($account_default_setting->bank_reference_ledger_rocket),
            'rocket_charge_ledger_id' =>  $this->getLedgerData($account_default_setting->rocket_charge_ledger),
            'bank_reference_ledger_nagad' =>  $this->getLedgerData($account_default_setting->bank_reference_ledger_nagad),
            'nagad_charge_ledger_id' =>  $this->getLedgerData($account_default_setting->nagad_charge_ledger),
        ];

        return $this->sendResponse($return_data, 'Data retrieve successfully done!');
    }

    public function setAccountDefaultSetting(Request $request)
    {
        $company_id = checkCompanyId($request);
        $this->validate($request, [
            'supplier_payable_account_type' => 'required',
            'supplier_discount_account_type'    => 'required',
            'supplier_advance_payment_account_type' => 'required',
            'bank_account_asset_type' => 'required',
            'bank_loan_account_liability_type' => 'required',
            'bank_charge_account_expense_type' => 'required',
            'bank_loan_interest_expense_type' => 'required',
            'bank_interest_income_type' => 'required',
            'customer_receivable_account_type' => 'required',

            'inventory_account' => 'required',
            'cogs_account'  => 'required',
            'inventory_damage_account'  => 'required',
            'inventory_adjustment_account'  => 'required',
            'petty_cash_account'    => 'required',
            'cash_in_hand_account'  => 'required',
        ]);

        $setting_data   = AccountDefaultSetting::where('company_id', $company_id)->first();

        $inputs = [
            'supplier_payable_account_type' => $request->get('supplier_payable_account_type'),
            'supplier_discount_account_type'    => $request->get('supplier_discount_account_type'),
            'supplier_advance_payment_account_type' => $request->get('supplier_advance_payment_account_type'),
            'bank_account_asset_type' => $request->get('bank_account_asset_type'),
            'bank_loan_account_liability_type' => $request->get('bank_loan_account_liability_type'),
            'bank_charge_account_expense_type' => $request->get('bank_charge_account_expense_type'),
            'bank_loan_interest_expense_type' => $request->get('bank_loan_interest_expense_type'),
            'bank_interest_income_type' => $request->get('bank_interest_income_type'),
            'customer_receivable_account_type' => $request->get('customer_receivable_account_type'),


            'inventory_account' => $request->get('inventory_account'),
            'cogs_account'  => $request->get('cogs_account'),
            'inventory_damage_account'  => $request->get('inventory_damage_account'),
            'inventory_adjustment_account'  => $request->get('inventory_adjustment_account'),
            'petty_cash_account'    => $request->get('petty_cash_account'),
            'cash_in_hand_account'  => $request->get('cash_in_hand_account'),
            'bank_account'  => $request->get('bank_account'), // Bank Transaction
            'bank_loan_account'  => $request->get('bank_loan_account'),
            'bank_loan_interest_expense_account'  => $request->get('bank_loan_interest_expense_account'),
            'bank_charge_expense_account'  => $request->get('bank_charge_expense_account'),
            'bank_interest_income_account'  => $request->get('bank_interest_income_account'),
            'trade_payable_account'  => $request->get('trade_payable_account'), // Purchase
            'supplier_discount_account'  => $request->get('supplier_discount_account'),
            'cash_sales_account'  => $request->get('cash_sales_account'), // sales
            'mfs_sales_account'  => $request->get('mfs_sales_account'),
            'bkash_sales_account'  => $request->get('bkash_sales_account'),
            'rocket_sales_account'  => $request->get('rocket_sales_account'),
            'nagad_sales_account'  => $request->get('nagad_sales_account'),
            'card_sales_account'  => $request->get('card_sales_account'),
            'credit_sales_account'  => $request->get('credit_sales_account'),
            'account_receivable_ledger'  => $request->get('account_receivable_ledger'),
            'customer_discount_account'  => $request->get('customer_discount_account'),
            'bank_reference_ledger_mfs'  => $request->get('bank_reference_ledger_mfs'),    // Reference Ledger
            'mfs_charge_ledger'  => $request->get('mfs_charge_ledger'),
            'bank_reference_ledger_bkash'  => $request->get('bank_reference_ledger_bkash'),
            'bkash_charge_ledger'  => $request->get('bkash_charge_ledger'),
            'bank_reference_ledger_rocket'  => $request->get('bank_reference_ledger_rocket'),
            'rocket_charge_ledger'  => $request->get('rocket_charge_ledger'),
            'bank_reference_ledger_nagad'  => $request->get('bank_reference_ledger_nagad'),
            'nagad_charge_ledger'  => $request->get('nagad_charge_ledger'),
            'company_id'  => $company_id,
        ];
        if($setting_data != '') {
            $setting_data_save =  $setting_data->update($inputs);
        }else{
            $setting_data_save = AccountDefaultSetting::create($inputs);
        }

        if($setting_data_save) {
            return $this->sendSuccess('Account default setting successfully done!');
        }else{
            return $this->sendError('Account default setting failed!');
        }
    }


    protected function getTypeData($type_id) {

        if($type_id != 0 && $type_id != '') {
            $type_data = AccountType::where('id', $type_id)->first();

            return $type_data->id . "___" . $type_data->type_code;
        }else{
            return null;
        }
    }

    protected function getLedgerData($ledger_id)
    {
        $ledger_data    = AccountLedger::where('id', $ledger_id)->first();

        if($ledger_data) {
            return $ledger_data->id . "___" . $ledger_data->ledger_code;
        }else{
            return null;
        }
    }
}
