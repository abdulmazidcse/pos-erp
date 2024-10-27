<?php

function getLedgerAccounts($company_id, $keys="", $specific_ledger="")
{
    $default_account_settings = \App\Models\AccountDefaultSetting::where('company_id', $company_id)->first();

    $default_payable_ledger = getLedgerAccountById($default_account_settings->trade_payable_account);

    $inventory_ledger   = getLedgerAccountById($default_account_settings->inventory_account);

    $cash_in_hand_ledger   = getLedgerAccountById($default_account_settings->cash_in_hand_account);

    $supplier_discount_ledger = getLedgerAccountById($default_account_settings->supplier_discount_account);

    if($specific_ledger != "") {
        $supplier_payable_ledger = getLedgerAccountById($specific_ledger->payable_ledger_id);

        if($supplier_payable_ledger) {
            $payable_ledger_code = $supplier_payable_ledger->ledger_code;
        }else{
            $payable_ledger_code = $default_payable_ledger->ledger_code;
        }

        $grn = [
            $inventory_ledger->ledger_code    => 'D',
            $payable_ledger_code    => 'C'
        ];

        $grn_payment = [
            $payable_ledger_code    => 'D',
            $cash_in_hand_ledger->ledger_code    => 'C',
        ];

        $purchase_return = [
            $payable_ledger_code    => 'D',
            $inventory_ledger->ledger_code    => 'C',
        ];
    }else{
        $grn = [
            $inventory_ledger->ledger_code    => 'D',
            $default_payable_ledger->ledger_code    => 'C'
        ];

        $grn_payment = [
            $default_payable_ledger->ledger_code     => 'D',
            $cash_in_hand_ledger->ledger_code    => 'C',
        ];

        $purchase_return = [
            $default_payable_ledger->ledger_code    => 'D',
            $inventory_ledger->ledger_code    => 'C',
        ];

    }
    $ledgers =  [
        "grn" => $grn,
        "grn_payment"   => $grn_payment,
        "purchase_return" => $purchase_return,
    ];

    if($keys != '') {
        return $ledgers[$keys];
    }else{
        return $ledgers;
    }
}


function getLedgerAccountById($ledger_id) {
    $ledger_account = \App\Models\AccountLedger::where('id', $ledger_id)->first();

    return $ledger_account;
}