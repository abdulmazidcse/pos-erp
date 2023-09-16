<?php

use App\Models\AccountClass;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\AccountVoucherTransaction;



function getAccountClassesWithBalance($accounts, $from=false, $to=false, $prevCloseBalance=true, $step = 0){

    if($step == 0) {
        $accounts = AccountClass::orderBy('code')->get();
    }

    $treeData = array();
    foreach ($accounts as $key => $account) {
        $children = getAccountTypesWithBalance('', $account->id, $from, $to, $prevCloseBalance);
        $debit_amount = collect($children)->where('parent', 0)->sum('debit_amount');
        $credit_amount = collect($children)->where('parent', 0)->sum('credit_amount');
        $opening_balance = collect($children)->where('parent', 0)->sum('opening_balance');
        $closing_balance = collect($children)->where('parent', 0)->sum('closing_balance');

        $positive_debit_amount = ($debit_amount < 0) ? $debit_amount * -1 : $debit_amount;
        $positive_credit_amount = ($credit_amount < 0) ? $credit_amount * -1 : $credit_amount;
        $treeData[] = [
            'id'    => $account->id,
            'code'   => $account->code,
            'name'   => $account->name,
            'account_type'  => "group",
            'debit_amount'  => number_format($debit_amount, 2, '.', ''),
            'credit_amount' => number_format($credit_amount, 2, '.', ''),
            'positive_debit_amount' => $positive_debit_amount,
            'positive_credit_amount'    => $positive_credit_amount,
            'opening_balance'   => $opening_balance,
            'closing_balance' => $closing_balance,
            'children'      => $children,
        ];

    }

    return $treeData;

}

function getAccountTypesWithBalance($types, $class_id, $from=false, $to=false, $prevCloseBalance=true, $step=0)
{
    if($step == 0) {
        $types  = AccountType::doesntHave('type_parents')->where('class_id', $class_id)->get();

    }

    if($step > 0) {
        $account_type = 'detail_type';
    }else{
        $account_type = 'type';
    }


    $treeData = array();
    $sl = 0;
    foreach ($types as $type) {

        if($prevCloseBalance) {
            $type_amount_data = getTypeClosingBalance($type->id, $from, $to);
            $type_opening_balance = $type_amount_data['opening_balance'];
            $type_closing_balance = $type_amount_data['balance'];
        }else{
            $type_opening_balance = 0;
            $type_closing_balance = 0;
        }

        $debit_amount = getAccountTypeBalance($type, 'dr', $from, $to);
        $credit_amount  = getAccountTypeBalance($type, 'cr', $from, $to);
        $positive_debit_amount = ($debit_amount < 0) ? $debit_amount * -1 : $debit_amount;
        $positive_credit_amount = ($credit_amount < 0) ? $credit_amount * -1 : $credit_amount;

        $treeData[$sl]  = [
            'id'    => $type->id,
            'parent'  => $type->parent_type_id,
            'code'  => $type->type_code,
            'name'  => $type->type_name,
            'account_type'  => $account_type,
            'debit_amount'    => $debit_amount,
            'credit_amount'    => $credit_amount,
            'positive_debit_amount'    => $positive_debit_amount,
            'positive_credit_amount'    => $positive_credit_amount,
            'opening_balance'   => number_format($type_opening_balance, 2, '.', ''),
            'closing_balance' => number_format($type_closing_balance, 2, '.', ''),
            'children' => getAccountTypesWithBalance($type->type_children, '', $from, $to, $prevCloseBalance, ($step + 1)),
        ];

        if($step > 0){
            $accounts = getAccounts($type->id);
            if(isset($accounts)) {
                foreach ($accounts as $account) {
                    $ledger_amount_data = ledgerClosingBalance($account->id, $from, $to);
                    $opening_balance = $ledger_amount_data['opening_balance'];
                    $closing_balance = $ledger_amount_data['balance'];

                    $debit_amount   = getLedgerAmount($account, 'dr', $from, $to);
                    $credit_amount  = getLedgerAmount($account, 'cr', $from, $to);
                    $positive_debit_amount = ($debit_amount < 0) ? $debit_amount * -1 : $debit_amount;
                    $positive_credit_amount = ($credit_amount < 0) ? $credit_amount * -1 : $credit_amount;

                    $treeData[$sl]['children'][] = [
                        'id'    => $account->id,
                        'code'  => $account->ledger_code,
                        'name'  => $account->ledger_name,
                        'account_type'  => "ledger",
                        'detail_type_id'  => $account->detail_type_id,
                        'debit_amount'    => $debit_amount,
                        'credit_amount'     => $credit_amount,
                        'positive_debit_amount'     => $positive_debit_amount,
                        'positive_credit_amount'     => $positive_credit_amount,
                        'opening_balance'   => number_format($opening_balance, 2, '.', ''),
                        'closing_balance' => number_format($closing_balance, 2, '.', ''),
                        'children'  => getSubLedgers($account, $from, $to),
                    ];
                }
            }
        }

        $sl++;
    }

    return $treeData;
}

// For Account Type Debit and Credit Amount
function getAccountTypeBalance($account_type, $type, $from = false, $to = false, $accounts = []){
//    $accounts = call_user_func_array('array_merge', getAllAccounts($account_type->id) ?: [[]]);
    $accounts = call_user_func_array('array_merge', getAllAccounts($account_type->id, [], getAllTypesAndLedgers()) ?: [[]]);

    ////////
    $accounts = array_merge($accounts, AccountLedger::where('detail_type_id', $account_type->id)->pluck('id')->toArray());

    $field = ($type == 'dr') ? 'debit' : 'credit';

    $amount = AccountVoucherTransaction::whereIn('ledger_id', $accounts)
        ->when($from, function($query) use($from){
            return $query->whereHas('account_vouchers', function($query) use($from){
                return $query->where('vdate', '>=', $from);
            });
        })
        ->when($to, function($query) use($to){
            return $query->whereHas('account_vouchers', function($query) use($to){
                return $query->where('vdate', '<=', $to);
            });
        })->where('vaccount_type', $type)->sum($field);



    return actualValue($account_type->account_classes, $type, number_format($amount, 2, '.', ''));
}

// Get Account Type Previous Opening Balance
function getPreviousAccountTypeBalance($account_type, $type, $start_date, $accounts = []){
//    $accounts = call_user_func_array('array_merge', getAllAccounts($account_type->id) ?: [[]]);
    $accounts = call_user_func_array('array_merge', getAllAccounts($account_type->id, [], getAllTypesAndLedgers()) ?: [[]]);
    $accounts = array_merge($accounts, AccountLedger::where('detail_type_id', $account_type->id)->pluck('id')->toArray());

    $field = ($type == 'dr') ? 'debit' : 'credit';

    $amount = AccountVoucherTransaction::whereIn('ledger_id', $accounts)
        ->when($start_date, function($query) use($start_date){
            return $query->whereHas('account_vouchers', function($query) use($start_date){
                return $query->where('vdate', '<', $start_date);
            });
        })->where('vaccount_type', $type)->sum($field);

    return actualValue($account_type->account_classes, $type, number_format($amount, 2, '.', ''));
}

function getTypeOpeningBalance($type, $prevCloseBalance=true){
//    $accounts = call_user_func_array('array_merge', getAllAccounts($type->id) ?: [[]]);
    $accounts = call_user_func_array('array_merge', getAllAccounts($type->id, [], getAllTypesAndLedgers()) ?: [[]]);
    ////////
    $accounts = array_merge($accounts, AccountLedger::where('detail_type_id', $type->id)->pluck('id')->toArray());

    // new work
    $total_opening_balance = 0;
    if($type->parent_type_id == 0) {
        $children   = getAccountTypesWithBalance($type->type_children, '', false, false, $prevCloseBalance, 1);
        $total_opening_balance = collect($children)->where('parent', $type->id)->sum('opening_balance');
    }else{
        foreach ($accounts as $key=>$account) {
            $account_data   = AccountLedger::with(['account_types'])->where('id', $account)->first();
            $ledger_opening_balance = actualValue($account_data->account_types->account_classes, $account_data->ledger_type, $account_data->opening_balance);

            $total_opening_balance += $ledger_opening_balance;

        }
    }

    $opening_balance = $total_opening_balance;


    return $opening_balance;
}

function getTypeClosingBalance($account_type_id, $from = false, $to = false, $accounts = []){
    $type = AccountType::find($account_type_id);
    $debit = getAccountTypeBalance($type, 'dr', $from, $to, $accounts);
    $credit = getAccountTypeBalance($type, 'cr', $from, $to, $accounts);
    $default_opening_balance = getTypeOpeningBalance($type);

    if($from && $to) {
        $type_prev_debit = getPreviousAccountTypeBalance($type, 'dr', $from, $accounts);
        $type_prev_credit = getPreviousAccountTypeBalance($type, 'cr', $from, $accounts);
        $type_balance = (float) $type_prev_debit + (float) $type_prev_credit;
        $opening_balance = $default_opening_balance + $type_balance;
    }else {
        $type_balance = 0;
        $opening_balance = $default_opening_balance + $type_balance;
    }

    return [
        'opening_balance' => $opening_balance,
        'debit' => $debit,
        'credit' => $credit,
        'balance' => (float)$debit+ (float) $credit + $opening_balance,
    ];
}



// For Get Account Ledger Debit and Credit Amount
function getLedgerAmount($account, $type, $from=false, $to=false)
{
    $field = ($type == 'dr') ? 'debit' : 'credit';
    $amount =  AccountVoucherTransaction::where('ledger_id', $account->id)
        ->when($from, function($query) use($from){
            return $query->whereHas('account_vouchers', function($query) use($from){
                return $query->where('vdate', '>=', $from);
            });
        })
        ->when($to, function($query) use($to){
            return $query->whereHas('account_vouchers', function($query) use($to){
                return $query->where('vdate', '<=', $to);
            });
        })->where('vaccount_type', $type)->sum($field);

    $class= $account->account_types->account_classes;
    return actualValue($class, $type, number_format($amount, 2, '.', ''));
}

function getPreviousLedgerAmount($account, $type, $start_date)
{
    $field = ($type == 'dr') ? 'debit' : 'credit';
    $amount =  AccountVoucherTransaction::where('ledger_id', $account->id)
        ->when($start_date, function($query) use($start_date){
            return $query->whereHas('account_vouchers', function($query) use($start_date){
                return $query->where('vdate', '<', $start_date);
            });
        })
        ->where('vaccount_type', $type)->sum($field);
    $class= $account->account_types->account_classes;

    return actualValue($class, $type, number_format($amount, 2, '.', ''));
}

function ledgerOpeningBalance($account){
    return actualValue($account->account_types->account_classes, $account->ledger_type, $account->opening_balance);
}



function ledgerClosingBalance($account_id, $from = false, $to = false){
    $account = AccountLedger::find($account_id);
    $default_opening_balance = 0;
    $opening_balance = 0;
    $debit = 0;
    $credit = 0;
    if(isset($account->id)){
        $debit = getLedgerAmount($account, 'dr', $from, $to);
        $credit = getLedgerAmount($account, 'cr', $from, $to);
        $default_opening_balance = ledgerOpeningBalance($account);
        if($from && $to) {
            $prev_debit = getPreviousLedgerAmount($account, 'dr', $from);
            $prev_credit = getPreviousLedgerAmount($account, 'cr', $from);
            $ledger_balance = (float) $prev_debit +  (float) $prev_credit;
            $opening_balance = (float) $default_opening_balance + $ledger_balance;
        }else{
            $ledger_balance = 0;
            $opening_balance = (float) $default_opening_balance + $ledger_balance;
        }
    }

    return [
        'opening_balance' => $opening_balance,
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $debit+$credit+$opening_balance,
    ];
}

function getSubLedgers($account, $from, $to){
    $subLedgers = AccountLedger::where('detail_type_id', $account->detail_type_id)->where('parent_id', $account->id)->orderBy('ledger_code')->get();
    $subArray = [];
    if(isset($subLedgers[0])){
        foreach ($subLedgers as $key => $subLedger){
            $ledger_amount_data = ledgerClosingBalance($subLedger->id, $from, $to);
            $opening_balance = $ledger_amount_data['opening_balance'];
            $closing_balance = $ledger_amount_data['balance'];

            $debit_amount   = getLedgerAmount($subLedger, 'dr', $from, $to);
            $credit_amount  = getLedgerAmount($subLedger, 'cr', $from, $to);
            $positive_debit_amount = ($debit_amount < 0) ? $debit_amount * -1 : $debit_amount;
            $positive_credit_amount = ($credit_amount < 0) ? $credit_amount * -1 : $credit_amount;

            $subArray[] = [
                'id'    => $subLedger->id,
                'code'  => $subLedger->ledger_code,
                'name'  => $subLedger->ledger_name,
                'account_type'  => "ledger",
                'detail_type_id'  => $account->detail_type_id,
                'debit_amount'    => $debit_amount,
                'credit_amount'     => $credit_amount,
                'positive_debit_amount'     => $positive_debit_amount,
                'positive_credit_amount'     => $positive_credit_amount,
                'opening_balance'   => number_format($opening_balance, 2, '.', ''),
                'closing_balance' => number_format($closing_balance, 2, '.', ''),
                'children'  => getSubLedgers($subLedger, $from, $to),
            ];
        }
    }

    return $subArray;
}

function getAccounts($type_id)
{
    $accounts = AccountLedger::with(['account_types'])->where('detail_type_id', $type_id)->orderBy('ledger_code')->where('parent_id', 0)->get();
    return $accounts;
}

// Update 03/09/2023
function getAllAccounts($account_type_id, $accounts = [], $all = []){
    $types = collect($all['types'])->where('parent_type_id', $account_type_id);
    $account_data = array();
    foreach ($types as $key => $type) {
//        echo $type->type_name." ". $type->id. "\n";
        $account_ledgers = collect($all['accounts'])->where('detail_type_id', $type->id);

        if ($account_ledgers->count() > 0) {
            array_push($accounts, $account_ledgers->pluck('id')->toArray());
            $account_data[] = $account_ledgers->pluck('id')->toArray();
        }

        $type_childrens = collect($all['types'])->where('parent_type_id', $type->id);
        if ($type_childrens->count() > 0) {
            $childAccountLedgers = getAllAccounts($type->id, $accounts, $all);
            $accounts = array_merge($account_data, $childAccountLedgers);
        }
    }

    return $accounts;
}

// Update 03/09/2023
function getAllTypesAndLedgers(){
    return [
        'types' => AccountType::with(['account_ledgers', 'type_children.account_ledgers'])->get(),
        'accounts' => AccountLedger::get(),
    ];
}



function actualValue($class, $type, $amount = 0){
    $positive = isPositive($class, $type);
    $amount = ($positive ? $amount : ($amount*(-1)));
    return ($amount == -0 ? number_format(0, 2, '.', '') : number_format($amount, 2, '.', ''));
}

function isPositive($class, $type)
{
    $positive = false;
    if ($type == "dr") {
        $positive = ($class->is_debit_positive == 1 ? true : false);
    } elseif ($type == "cr") {
        $positive = ($class->is_credit_positive == 1 ? true : false);
    }

    return $positive;

}