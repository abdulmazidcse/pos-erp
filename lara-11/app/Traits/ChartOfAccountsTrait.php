<?php
namespace App\Traits;


use App\Models\AccountClass;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;

trait ChartOfAccountsTrait
{
    //    public function getChartOfAccountsList($accounts, $step = 0){
    //
    //        if($step == 0) {
    //            $accounts = $this->doesntHave('parent')->orderBy('ledger_code')->get();
    //        }
    //
    //        $treeData = array();
    //        foreach ($accounts as $key => $account) {
    //            $treeData[] = [
    //                'id'    => $account->id,
    //                'ledger_code'   => $account->ledger_code,
    //                'ledger_name'   => $account->ledger_name,
    //                'parent_id'     => $account->parent_id,
    //                'type_id'       => $account->type_id,
    //                'type_name'       => $account->account_types->type_name ?? 'N/A',
    //                'is_control_transaction'    => $account->is_control_transaction,
    //                'is_master_head'    => $account->is_master_head,
    //                'children'      => $this->getChartOfAccountsList($account->children_accounts, $step + 1),
    //            ];
    //
    //        }
    //
    //        return $treeData;
    //
    //    }

    public function getChartOfAccountsList($company_id, $accounts, $step = 0){

        if($step == 0) {
            $accounts = AccountClass::where('company_id', $company_id)->orderBy('code')->get();
        }

        $treeData = array();
        foreach ($accounts as $key => $account) {
            $treeData[] = [
                'id'    => $account->id,
                'code'   => $account->code,
                'name'   => $account->name,
                'company_id'   => $account->company_id,
                'account_type'  => "group",
                'account_type_name'  => ucwords(strtolower($account->name)),
                'children'      => $this->getAccountTypes('', $account->id),
            ];

        }

        return $treeData;

    }

    public function getAccountTypes($types, $class_id, $step=0)
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
            if($step == 0) {
                $account_type_name = ucwords(strtolower($type->account_classes->name));
            }else{
                $account_type_name = ucwords(strtolower($type->type_parents->type_name));
            }
            $treeData[$sl]  = [
                'id'    => $type->id,
                'code'  => $type->type_code,
                'name'  => $type->type_name,
                'company_id'  => $type->company_id,
                'account_type'  => $account_type,
                'account_type_name'  => $account_type_name,
                'children' => $this->getAccountTypes($type->type_children, '',($step + 1)),
            ];

            if($step > 0){
                $accounts = $this->getAccounts($type->id);
                if(isset($accounts)) {
                    foreach ($accounts as $account) {
                        $children = $this->getSubLedgers($account);
                        if(count($children) > 0) {
                            $children_data = ['children' => $children];
                        }else{
                            $children_data = [];
                        }
                    //                        $treeData[$sl]['children'][] = [
                    //                            'id'    => $account->id,
                    //                            'code'  => $account->ledger_code,
                    //                            'name'  => $account->ledger_name,
                    //                            'account_type'  => "ledger",
                    //                            'account_type_name'  => ucwords(strtolower($account->account_types->type_name)),
                    //                            'detail_type_id'  => $account->detail_type_id,
                    ////                            'children'  => $children,
                    ////                            $children_data,
                    //                        ];

                        $treeData[$sl]['children'][] = array_merge([
                            'id'    => $account->id,
                            'code'  => $account->ledger_code,
                            'name'  => $account->ledger_name,
                            'company_id'  => $account->company_id,
                            'account_type'  => "ledger",
                            'account_type_name'  => ucwords(strtolower($account->account_types->type_name)),
                            'detail_type_id'  => $account->detail_type_id,
                        ], $children_data);
                    }
                }
            }
            
            $sl++;
        }

        return $treeData;
    }



    public function getOnlyAccountTypes($types, $class_id, $step=0)
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
            if($step == 0) {
                $account_type_name = ucwords(strtolower($type->account_classes->name));
            }else{
                $account_type_name = ucwords(strtolower($type->type_parents->type_name));
            }
            $treeData[$sl]  = [
                'id'    => $type->id,
                'code'  => $type->type_code,
                'name'  => $type->type_name,
                'company_id'  => $type->company_id,
                'account_type'  => $account_type,
                'account_type_name'  => $account_type_name,
                'children' => $this->getOnlyAccountTypes($type->type_children, '',($step + 1)),
            ];

            $sl++;
        }

        return $treeData;
    }

    public function getAccountDetailTypes($types, $class_id, $step=0) {
        if($step == 0) {
            $types  = AccountType::doesntHave('type_parents')->where('class_id', $class_id)->get();
        }else if($step == 1){
            $types = AccountType::where('parent_type_id', $types->id)->get();
        }

        if($step > 0) {
            $account_type = 'detail_type';
        }else{
            $account_type = 'type';
        }


        $treeData = array();
        $sl = 0;
        foreach ($types as $type) {
            if($step == 0) {
                $account_type_name = ucwords(strtolower($type->account_classes->name));
            }else{
                $account_type_name = ucwords(strtolower($type->type_parents->type_name));
            }
            if(count($type->type_children) > 0) {
                $children = $this->getAccountDetailTypes($type->type_children, '',($step + 1));
            }else{
                $children = [];
            }
            $treeData[$sl]  = [
                'id'    => $type->id,
                'code'  => $type->type_code,
                'name'  => $type->type_name,
                'company_id'  => $type->company_id,
                'label'  => $type->type_name,
                'account_type'  => $account_type,
                'account_type_name'  => $account_type_name,
                'children' => $children,
            ];

            $sl++;
        }

        return $treeData;
    }

    public function getAccounts($type_id)
    {
        $accounts = $this->where('detail_type_id', $type_id)->orderBy('ledger_code')->where('parent_id', 0)->get();
        return $accounts;
    }

    public function getSubLedgers($account){
        $subLedgers = $this->where('detail_type_id', $account->detail_type_id)->where('parent_id', $account->id)->orderBy('ledger_code')->get();
        $subArray = [];
        if(isset($subLedgers[0])){
            foreach ($subLedgers as $key => $subLedger){
                $children = $this->getSubLedgers($subLedger);
                if(count($children) > 0) {
                    $children_data = ['children' => $children];
                }else{
                    $children_data = [];
                }
                //                $subArray[] = [
                //                    'id'    => $subLedger->id,
                //                    'code'  => $subLedger->ledger_code,
                //                    'name'  => $subLedger->ledger_name,
                //                    'account_type'  => "ledger",
                //                    'account_type_name'  => ucwords(strtolower($account->account_types->type_name)),
                //                    'detail_type_id'  => $account->detail_type_id,
                ////                    'children'  => $this->getSubLedgers($subLedger),
                //                ];

                $subArray[] = array_merge([
                    'id'    => $subLedger->id,
                    'code'  => $subLedger->ledger_code,
                    'name'  => $subLedger->ledger_name,
                    'company_id'  => $subLedger->company_id,
                    'account_type'  => "ledger",
                    'account_type_name'  => ucwords(strtolower($account->account_types->type_name)),
                    'detail_type_id'  => $account->detail_type_id,
                ], $children_data);
            }
        }

        return $subArray;
    }



    // AccountsOptions
    public function getChartOfAccountOptions($company_id, $accounts, $step = 0, $chosen=0){

        if($step == 0) {
            $accounts = AccountLedger::doesntHave('parent')->where('company_id', $company_id)->orderBy('ledger_code')->get();
        }
        $treeData = array();
        foreach ($accounts as $key => $account) {
            $treeData[] = [
                'id'    => $account->id,
                'code'  => $account->ledger_code,
                'name'  => $account->ledger_name,
                'account_type'  => "ledger",
                'account_type_name'  => ucwords(strtolower($account->account_types->type_name)),
                'detail_type_id'  => $account->detail_type_id,
                'children'  => $this->getChartOfAccountOptions($company_id, $account->children_accounts()->where('is_control_transaction', 0)->get(), $step + 1),
            ]; 
        }

        return $treeData;

    }


    // Get All Accounts
    function getAllAccounts($account_type_id, $accounts = []){
        $types = AccountType::with('account_ledgers', 'type_children')->where('parent_id', $account_type_id)->orderBy('type_code')->get();
        foreach ($types as $key => $type) {
            if($type->account_ledgers->count() > 0){
                array_push($accounts, $type->account_ledgers->pluck('id')->toArray());
            }

            if($type->type_children->count() > 0){
                return $this->getAllAccounts($type->id, $accounts);
            }
        }

        return $accounts;
    }

    function groupDebitBalance($account_type_id, $from = false, $to = false, $accounts = []){
        $accounts = call_user_func_array('array_merge', $this->getAllAccounts($account_type_id) ?: [[]]);
        $accounts = array_merge($accounts, AccountLedger::where('detail_type_id', $account_type_id)->pluck('id')->toArray());

        return AccountVoucherTransaction::whereIn('ledger_id', $accounts)
            ->when($from, function($query) use($from){
                return $query->whereHas('account_vouchers', function($query) use($from){
                    return $query->where('vdate', '>=', $from);
                });
            })
            ->when($to, function($query) use($to){
                return $query->whereHas('account_vouchers', function($query) use($to){
                    return $query->where('vdate', '<=', $to);
                });
            })->where('vaccount_type', 'dr')->sum('amount');
    }

    function groupCreditBalance($account_type_id, $from = false, $to = false, $accounts = []){
        $accounts = call_user_func_array('array_merge', $this->getAllAccounts($account_type_id) ?: [[]]);
        $accounts = array_merge($accounts, AccountLedger::where('detail_type_id', $account_type_id)->pluck('id')->toArray());

        return AccountVoucherTransaction::whereIn('ledger_id', $accounts)
            ->when($from, function($query) use($from){
                return $query->whereHas('account_vouchers', function($query) use($from){
                    return $query->where('vdate', '>=', $from);
                });
            })
            ->when($to, function($query) use($to){
                return $query->whereHas('account_vouchers', function($query) use($to){
                    return $query->where('vdate', '<=', $to);
                });
            })->where('vaccount_type', 'cr')->sum('amount');
    }


}