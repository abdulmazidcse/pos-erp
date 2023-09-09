<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\LedgerBookResource;
use App\Models\AccountClass;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountReportAPIController extends AppBaseController
{
    public function reportDailyTransaction(Request $request)
    {
        $from_date = $request->get('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date    = $request->get('to_date') ?? Carbon::now()->format("Y-m-d");

        $vouchers = AccountVoucher::when($from_date, function ($query) use ($from_date) {
                                        return $query->where('vdate', '>=', $from_date);
                                    })
                                    ->when($to_date, function ($query) use ($to_date) {
                                        return $query->where('vdate', '<=', $to_date);
                                    })->orderBy('vdate', 'desc')->get();
        $transaction_array = [];
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        foreach ($vouchers as $key => $voucher){
            if($voucher->account_voucher_transactions->count() >0){
                foreach ($voucher->account_voucher_transactions as $key => $transaction){
                    array_push($transaction_array, [
                        'id'    => $transaction->id,
                        'vdate' => $voucher->vdate,
                        'vcode' => $voucher->vcode,
                        'vtype' => ($voucher->vtype_id != 0) ? ucfirst($voucher->vtype_value) : "Auto",
                        'ledger_head'   => $transaction->account_ledgers->ledger_name.' ['.$transaction->account_ledgers->ledger_code.']',
                        'notes' => ($transaction->voucher_note) ? $transaction->voucher_note : $voucher->global_note,
                        'debit_amount'  => ($transaction->debit != 0) ? number_format($transaction->debit, 2, '.', '') : '---',
                        'credit_amount' => ($transaction->credit != 0) ? number_format($transaction->credit, 2, '.', '') : '---',
                    ]);

                    $total_debit_amount += $transaction->debit;
                    $total_credit_amount += $transaction->credit;
                }
            }
        }


        $return_data    = [
            'transactions'  => $transaction_array,
            'total_debit_amount'    => number_format($total_debit_amount, 2, '.', ''),
            'total_credit_amount'   => number_format($total_credit_amount, 2, '.', ''),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];

//        $transaction_data = AccountVoucherTransaction::with(['account_vouchers', 'account_ledgers'])
//            ->when($from_date, function ($query) use ($from_date) {
//                return $query->whereHas('account_vouchers', function ($query) use ($from_date){
//                   return $query->where('vdate', '>=', $from_date);
//                });
//            })->when($to_date, function ($query) use ($to_date) {
//                return $query->whereHas('account_vouchers', function ($query) use ($to_date){
//                   return $query->where('vdate', '<=', $to_date);
//                });
//            })
//            ->get();
//
//        $return_data    = $transaction_data->map(function ($item) {
//            return [
//                'id'    => $item->id,
//                'vdate' => $item->account_vouchers->vdate,
//                'vcode' => $item->account_vouchers->vcode,
//                'vtype' => ($item->account_vouchers->vtype_id != 0) ? ucfirst($item->account_vouchers->vtype_value) : "Auto",
//                'ledger_head'   => $item->account_ledgers->ledger_name.' ['.$item->account_ledgers->ledger_code.']',
//                'notes' => $item->voucher_note,
//                'debit_amount'  => $item->debit,
//                'credit_amount' => $item->credit,
//            ];
//        });

        return $this->sendResponse($return_data, 'Transaction Data retrieve successfully!');

    }

    public function reportLedgerTransaction(Request $request) {

        $ledger_id  = $request->get('ledger_id');
        $from_date = $request->get('from_date') ?? date("Y-m-01");
        $to_date    = $request->get('to_date') ?? date("Y-m-d");

        if(empty($ledger_id)) {
            return $this->sendError("Please select at least any one ledger");
        }

        $account    = AccountLedger::where('id', $ledger_id)->first();
        $account_class = $account->account_types->account_classes;

        $debit_positive = isPositive($account_class, "dr");
        $credit_positive = isPositive($account_class, "cr");

        if($debit_positive) {
            $debit_amount_action = 1;
        }else{
            $debit_amount_action = -1;
        }

        if($credit_positive) {
            $credit_amount_action = 1;
        }else{
            $credit_amount_action = -1;
        }


        $account_opening_balance = ledgerOpeningBalance($account);
        $previous_debit_amount = getPreviousLedgerAmount($account, 'dr', $from_date);
        $previous_credit_amount = getPreviousLedgerAmount($account, 'cr', $from_date);

        $account_balance = ($previous_debit_amount + $previous_credit_amount) + $account_opening_balance;

        $report_data = AccountVoucherTransaction::with(['account_vouchers'])->where('ledger_id', $ledger_id)
                        ->when($from_date, function ($query) use ($from_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($from_date){
                                return $query->where('vdate', '>=', $from_date);
                            });
                        })->when($to_date, function ($query) use ($to_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($to_date){
                                return $query->where('vdate', '<=', $to_date);
                            });
                        })->get();

//    return $report_data;


        $return_data = [
            'opening_balance'   => $account_balance,
            'report_data'   => LedgerBookResource::collection($report_data),
            'debit_amount_action'   => $debit_amount_action,
            'credit_amount_action'  => $credit_amount_action,
        ];


        return $this->sendResponse($return_data, 'Ledger Report Data Retrieve Successfully');


    }

    public function reportCashBookLedgerTransaction(Request $request) {

        $ledger_id  = $request->get('ledger_id');
        $from_date = $request->get('from_date') ?? date("Y-m-01");
        $to_date    = $request->get('to_date') ?? date("Y-m-d");

        if(empty($ledger_id)) {
            return $this->sendError("Please select at least any one ledger");
        }

        $account    = AccountLedger::where('id', $ledger_id)->first();
        $account_class = $account->account_types->account_classes;

        $debit_positive = isPositive($account_class, "dr");
        $credit_positive = isPositive($account_class, "cr");

        if($debit_positive) {
            $debit_amount_action = 1;
        }else{
            $debit_amount_action = -1;
        }

        if($credit_positive) {
            $credit_amount_action = 1;
        }else{
            $credit_amount_action = -1;
        }



        $account_opening_balance = ledgerOpeningBalance($account);
        $previous_debit_amount = getPreviousLedgerAmount($account, 'dr', $from_date);
        $previous_credit_amount = getPreviousLedgerAmount($account, 'cr', $from_date);

        $account_balance = ($previous_debit_amount + $previous_credit_amount) + $account_opening_balance;

        $report_data = AccountVoucherTransaction::with(['account_vouchers'])->where('ledger_id', $ledger_id)
                        ->when($from_date, function ($query) use ($from_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($from_date){
                                return $query->where('vdate', '>=', $from_date);
                            });
                        })->when($to_date, function ($query) use ($to_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($to_date){
                                return $query->where('vdate', '<=', $to_date);
                            });
                        })->get();

//    return $report_data;


        $return_data = [
            'opening_balance'   => $account_balance,
            'report_data'   => LedgerBookResource::collection($report_data),
            'debit_amount_action'   => $debit_amount_action,
            'credit_amount_action'  => $credit_amount_action,
        ];


        return $this->sendResponse($return_data, 'Ledger Report Data Retrieve Successfully');


    }

    public function reportBankBookLedgerTransaction(Request $request) {

        $ledger_id  = $request->get('ledger_id');
        $from_date = $request->get('from_date') ?? date("Y-m-01");
        $to_date    = $request->get('to_date') ?? date("Y-m-d");

        if(empty($ledger_id)) {
            return $this->sendError("Please select at least any one ledger");
        }

        $account    = AccountLedger::where('id', $ledger_id)->first();
        $account_class = $account->account_types->account_classes;

        $debit_positive = isPositive($account_class, "dr");
        $credit_positive = isPositive($account_class, "cr");

        if($debit_positive) {
            $debit_amount_action = 1;
        }else{
            $debit_amount_action = -1;
        }

        if($credit_positive) {
            $credit_amount_action = 1;
        }else{
            $credit_amount_action = -1;
        }



        $account_opening_balance = ledgerOpeningBalance($account);
        $previous_debit_amount = getPreviousLedgerAmount($account, 'dr', $from_date);
        $previous_credit_amount = getPreviousLedgerAmount($account, 'cr', $from_date);

        $account_balance = ($previous_debit_amount + $previous_credit_amount) + $account_opening_balance;

        $report_data = AccountVoucherTransaction::with(['account_vouchers'])->where('ledger_id', $ledger_id)
                        ->when($from_date, function ($query) use ($from_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($from_date){
                                return $query->where('vdate', '>=', $from_date);
                            });
                        })->when($to_date, function ($query) use ($to_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($to_date){
                                return $query->where('vdate', '<=', $to_date);
                            });
                        })->get();

//    return $report_data;


        $return_data = [
            'opening_balance'   => $account_balance,
            'report_data'   => LedgerBookResource::collection($report_data),
            'debit_amount_action'   => $debit_amount_action,
            'credit_amount_action'  => $credit_amount_action,
        ];


        return $this->sendResponse($return_data, 'Ledger Report Data Retrieve Successfully');


    }

    public function reportCashAndBankBookLedgerTransaction(Request $request) {

        $ledger_id  = $request->get('ledger_id');
        $from_date = $request->get('from_date') ?? date("Y-m-01");
        $to_date    = $request->get('to_date') ?? date("Y-m-d");

        if(empty($ledger_id)) {
            return $this->sendError("Please select at least any one ledger");
        }

        $account    = AccountLedger::where('id', $ledger_id)->first();
        $account_class = $account->account_types->account_classes;

        $debit_positive = isPositive($account_class, "dr");
        $credit_positive = isPositive($account_class, "cr");

        if($debit_positive) {
            $debit_amount_action = 1;
        }else{
            $debit_amount_action = -1;
        }

        if($credit_positive) {
            $credit_amount_action = 1;
        }else{
            $credit_amount_action = -1;
        }



        $account_opening_balance = ledgerOpeningBalance($account);
        $previous_debit_amount = getPreviousLedgerAmount($account, 'dr', $from_date);
        $previous_credit_amount = getPreviousLedgerAmount($account, 'cr', $from_date);

        $account_balance = ($previous_debit_amount + $previous_credit_amount) + $account_opening_balance;

        $report_data = AccountVoucherTransaction::with(['account_vouchers'])->where('ledger_id', $ledger_id)
                        ->when($from_date, function ($query) use ($from_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($from_date){
                                return $query->where('vdate', '>=', $from_date);
                            });
                        })->when($to_date, function ($query) use ($to_date){
                            return $query->whereHas('account_vouchers', function ($query) use ($to_date){
                                return $query->where('vdate', '<=', $to_date);
                            });
                        })->get();

//    return $report_data;


        $return_data = [
            'opening_balance'   => $account_balance,
            'report_data'   => LedgerBookResource::collection($report_data),
            'debit_amount_action'   => $debit_amount_action,
            'credit_amount_action'  => $credit_amount_action,
        ];


        return $this->sendResponse($return_data, 'Ledger Report Data Retrieve Successfully');


    }

    public function reportTrialBalance(Request $request)
    {
        set_time_limit(600);
        $current_date = date("Y-m-d");
        $inputs = $request->all();
        if(count($inputs) > 0) {
            $from_date = $request->get('from_date');
            $to_date    = $request->get('to_date');
        }else{
            $from_date = date("Y-m-01");
            $to_date = $current_date;
        }
        $accounts = getAccountClassesWithBalance('', $from_date, $to_date);


        $total_opening_balance = collect($accounts)->where('account_type', 'group')->sum('opening_balance');
        $total_debit_balance = collect($accounts)->where('account_type', 'group')->sum('positive_debit_amount');
        $total_credit_balance = collect($accounts)->where('account_type', 'group')->sum('positive_credit_amount');
        $total_closing_balance = collect($accounts)->where('account_type', 'group')->sum('closing_balance');

        $return_data    = [
            'accounts'    => $accounts,
            'total_opening_balance'    => returnDecimalNumber($total_opening_balance),
            'total_debit_balance'    => returnDecimalNumber($total_debit_balance),
            'total_credit_balance'    => returnDecimalNumber($total_credit_balance),
            'total_closing_balance'    => returnDecimalNumber($total_closing_balance),
            'from_date' => $from_date,
            'to_date'   => $to_date
        ];

        return $this->sendResponse($return_data, 'Chart of Accounts Retrieve Successfully!');
    }

    public function reportProfitLoss(Request $request)
    {
        $expense_class = AccountClass::where('code', 5)->first();
        $income_class = AccountClass::where('code', 4)->first();

        $from_date = '';
        if($request->has('from_date') && $request->get('from_date')) {
            $from_date = date("Y-m-d", strtotime($request->get('from_date')));

        }
        $to_date    = '';
        if($request->has('to_date') && $request->get('to_date')) {
            $to_date = date("Y-m-d", strtotime($request->get('to_date')));

        }

        if($from_date && $to_date) {

            $expense_accounts = getAccountTypesWithBalance('', $expense_class->id, $from_date, $to_date, false);
            $income_accounts = getAccountTypesWithBalance('', $income_class->id, $from_date, $to_date, false);

        } else {

            $expense_accounts = getAccountTypesWithBalance('', $expense_class->id, false, false);
            $income_accounts = getAccountTypesWithBalance('', $income_class->id, false, false);

        }

        $expense_balance = collect($expense_accounts)->where('parent', 0)->sum('opening_balance') + collect($expense_accounts)->where('parent', 0)->sum('debit_amount') + collect($expense_accounts)->where('parent', 0)->sum('credit_amount');

        $income_balance = collect($income_accounts)->where('parent', 0)->sum('opening_balance') + collect($income_accounts)->where('parent', 0)->sum('debit_amount') + collect($income_accounts)->where('parent', 0)->sum('credit_amount');
        $return_data    = [
            'expense_accounts'    => $expense_accounts,
            'income_accounts'   => $income_accounts,
            'expense_balance'   => number_format($expense_balance, 2, '.', ''),
            'income_balance'    => number_format($income_balance, 2, '.', ''),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];

        return $this->sendResponse($return_data, 'Income Statement Retrieve Successfully!');
    }

    public function reportBalanceSheet(Request $request)
    {
        $asset_class = AccountClass::where('code', 1)->first();
        $liability_class = AccountClass::where('code', 2)->first();
        $equity_class = AccountClass::where('code', 3)->first();

        $to_date = '';
        if($request->has('as_on_date') && $request->get('as_on_date')){
            $from_date = date("Y-m-d", strtotime($request->get('as_on_date')));
            $to_date = date("Y-m-d", strtotime($request->get('as_on_date')));

            $asset_accounts = getAccountTypesWithBalance('', $asset_class->id, false, $to_date);
            $liability_accounts = getAccountTypesWithBalance('', $liability_class->id, false, $to_date);
            $equity_accounts = getAccountTypesWithBalance('', $equity_class->id, false, $to_date);
        }else {
            $asset_accounts = getAccountTypesWithBalance('', $asset_class->id);
            $liability_accounts = getAccountTypesWithBalance('', $liability_class->id);
            $equity_accounts = getAccountTypesWithBalance('', $equity_class->id);
        }

        $asset_balance = collect($asset_accounts)->where('parent', 0)->sum('opening_balance') + collect($asset_accounts)->where('parent', 0)->sum('debit_amount')+collect($asset_accounts)->where('parent', 0)->sum('credit_amount');
        $liability_balance = collect($liability_accounts)->where('parent', 0)->sum('opening_balance') + collect($liability_accounts)->where('parent', 0)->sum('debit_amount')+collect($liability_accounts)->where('parent', 0)->sum('credit_amount');
        $equity_balance = collect($equity_accounts)->where('parent', 0)->sum('opening_balance') + collect($equity_accounts)->where('parent', 0)->sum('debit_amount')+collect($equity_accounts)->where('parent', 0)->sum('credit_amount') ?? 0;

        /** Surplus/Deficit from income statement */
        $expense_class = AccountClass::where('code', 5)->first();
        $income_class = AccountClass::where('code', 4)->first();

        if($to_date) {
            $expense_accounts = getAccountTypesWithBalance('', $expense_class->id, false, $to_date);
            $income_accounts = getAccountTypesWithBalance('', $income_class->id, false, $to_date);
        }else {
            $expense_accounts = getAccountTypesWithBalance('', $expense_class->id);
            $income_accounts = getAccountTypesWithBalance('', $income_class->id);
        }

        $expense_balance = collect($expense_accounts)->where('parent', 0)->sum('opening_balance') + collect($expense_accounts)->where('parent', 0)->sum('debit_amount') + collect($expense_accounts)->where('parent', 0)->sum('credit_amount') ?? 0;

        $income_balance = collect($income_accounts)->where('parent', 0)->sum('opening_balance') + collect($income_accounts)->where('parent', 0)->sum('debit_amount') + collect($income_accounts)->where('parent', 0)->sum('credit_amount') ?? 0;

        $total_equity_balance = ($equity_balance + ($income_balance - $expense_balance));

        $surplus_data   = array(
            [
                'id'    => '',
                'code'  => '',
                'name'   => 'Surplus/Deficit from Income Statement',
                'account_type'  => "ledger",
                'detail_type_id'  => '',
                'debit_amount'  => number_format(($expense_balance * -1), 2, '.', ''),
                'credit_amount' => number_format($income_balance, 2, '.', ''),
                'positive_debit_amount'     => 0,
                'positive_credit_amount'     => 0,
                'opening_balance'   => 0,
                'closing_balance' => 0,
                'children'  => [],
            ],
            [
                'id'    => '',
                'code'  => '',
                'name'   => 'Total Equity',
                'account_type'  => "Type",
                'detail_type_id'  => '',
                'debit_amount'  => number_format($total_equity_balance, 2, '.', ''),
                'credit_amount' => number_format(0, 2, '.', ''),
                'positive_debit_amount'     => 0,
                'positive_credit_amount'     => 0,
                'opening_balance'   => 0,
                'closing_balance' => 0,
                'children'  => [],
            ],

        );

        $equity_accounts = array_merge($equity_accounts, $surplus_data);
        /** @var  $return_data */
        $return_data    = [
            'assets_accounts'    => $asset_accounts,
            'liability_equity_accounts'   => array_merge($liability_accounts, $equity_accounts),
            'asset_balance' => number_format($asset_balance, 2, '.', ''),
            'liability_equity_balance' => number_format($liability_balance + $total_equity_balance, 2, '.', ''),
            'expense_balance' => $expense_balance,
            'income_balance' => $income_balance,
            'as_on_date'    => $to_date,
        ];

        return $this->sendResponse($return_data, 'Balance Sheet Data Retrieve Successfully!');
    }
}
