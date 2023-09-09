<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountClass;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function testTrialBalance(Request $request)
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

        return view('welcome', $return_data);
    }

    public function testProfitLoss(Request $request)
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

        return view('welcome', $return_data);
    }

    public function testBalanceSheet(Request $request)
    {
        $classes    = AccountClass::all();
        $asset_class = collect($classes)->where('code', 1)->first();
        $liability_class = collect($classes)->where('code', 2)->first();
        $equity_class = collect($classes)->where('code', 3)->first();

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

        return view('welcome', $return_data);
    }
}
