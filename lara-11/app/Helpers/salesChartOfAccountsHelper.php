<?php

function getSalesLedgerAccounts($keys="", $specific_ledger="")
{
    if($specific_ledger != "") {

        $sales = [
            '120101'    => 'D',
            '210301'    => 'C'
        ];

        $sales_return  = [
            '210301'    => 'D',
            '120701'    => 'C'
        ];
    }else{
        $sales = [
            '120101'    => 'D',
            '210301'    => 'C'
        ];

        $sales_return  = [
            '210301'    => 'D',
            '120701'    => 'C'
        ];
    }


    $ledgers =  [
        "sales" => $sales,
        "sales_return"   => $sales_return,
    ];

    if($keys != '') {
        return $ledgers[$keys];
    }else{
        return $ledgers;
    }
}