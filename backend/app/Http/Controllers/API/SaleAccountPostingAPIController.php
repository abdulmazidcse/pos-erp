<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CustomerResource;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\CostCenter;
use App\Models\MobileWallet;
use App\Models\PaymentCollection;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaleAccountPostingAPIController extends AppBaseController
{
    public function saleAccountPostingList(Request $request)
    {
        $this->validate($request, [
            'outlet_id'  => 'required',
            'sales_type'  => 'required',
            'from_date'   => 'required',
            'to_date'   => 'required',
        ]);

        $outlet_id  = $request->input('outlet_id');
        $sales_type = $request->input('sales_type');
        $from_date  = $request->input('from_date');
        $to_date    = $request->input('to_date');

        $company_id =  checkCompanyIdByOutletId($request); 

        $query   = Sale::with(['payments', 'salesItems'])->where('outlet_id', $outlet_id)
                                        ->whereIn('account_post_status', [0,1])
                                        ->whereDate('created_at', '>=', $from_date)
                                        ->whereDate('created_at', '<=', $to_date);



        $sale_non_posted_list   = $query->get();

        if(empty($sale_non_posted_list)) {
            return $this->sendError("Sales data not found!");
        }

        $final_non_posted_list  = [];
        foreach ($sale_non_posted_list as $snplist) {
            if($snplist->status == "paid" || $snplist->status == "partial") {
                $payments_data  = $snplist->payments()->where('post_status', 0)->get();
                foreach ($payments_data as $payment) {
                    if($payment->paying_by == "CC"){
                        $final_non_posted_list['credit_card'][]    = [
                            'sale_id'       => $snplist->id,
                            'customer_id'   => $snplist->customer_id,
                            'payment_id'    => $payment->id,
                            'bank_id'       => $payment->bank_id,
                            'amount'        => $payment->amount,
                            'sale_date'     => date("Y-m-d", strtotime($snplist->created_at)),
                        ];
                    }elseif($payment->paying_by == "mfs") {
                        if($payment->mobile_wallets) {
                            $wallet_name    = strtolower($payment->mobile_wallets->mobile_wallet);
                            if( $wallet_name == "bkash") {
                                $final_non_posted_list['bkash'][]    = [
                                    'sale_id'       => $snplist->id,
                                    'customer_id'   => $snplist->customer_id,
                                    'payment_id'    => $payment->id,
                                    'wallet_id' => $payment->wallet_id,
                                    'bank_id'   => $payment->bank_id,
                                    'amount'    => $payment->amount,
                                    'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                                ];
                            }
                            if( $wallet_name == "nagad") {
                                $final_non_posted_list['nagad'][]    = [
                                    'sale_id'       => $snplist->id,
                                    'customer_id'   => $snplist->customer_id,
                                    'payment_id'    => $payment->id,
                                    'wallet_id' => $payment->wallet_id,
                                    'bank_id'   => $payment->bank_id,
                                    'amount'    => $payment->amount,
                                    'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                                ];
                            }
                            if( $wallet_name == "rocket") {
                                $final_non_posted_list['rocket'][]    = [
                                    'sale_id'       => $snplist->id,
                                    'customer_id'   => $snplist->customer_id,
                                    'payment_id'    => $payment->id,
                                    'wallet_id' => $payment->wallet_id,
                                    'bank_id'   => $payment->bank_id,
                                    'amount'    => $payment->amount,
                                    'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                                ];
                            }


                        }
                    }elseif($payment->paying_by == "gift_card") {
                        $final_non_posted_list['gift_card'][]    = [
                            'sale_id'       => $snplist->id,
                            'customer_id'   => $snplist->customer_id,
                            'payment_id' => $payment->id,
                            'amount'    => $payment->amount,
                            'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                        ];
                    }elseif($payment->paying_by == "redeem_point") {
                        $final_non_posted_list['redeem_point'][]    = [
                            'sale_id'       => $snplist->id,
                            'customer_id'   => $snplist->customer_id,
                            'payment_id'    => $payment->id,
                            'amount'    => $payment->amount,
                            'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                        ];
                    }else{
                        $final_non_posted_list['cash'][]    = [
                            'sale_id'       => $snplist->id,
                            'customer_id'   => $snplist->customer_id,
                            'payment_id'    => $payment->id,
                            'amount'    => $payment->amount,
                            'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                        ];
                    }
                }
            }

            // this for credit sale
            if($snplist->status == "due" || $snplist->status == "partial") {
                $final_non_posted_list['credit_sale'][]    = [
                    'sale_id'       => $snplist->id,
                    'customer_id'   => $snplist->customer_id,
                    'amount'    => $snplist->grand_total - $snplist->collection_amount,
                    'sale_date' => date("Y-m-d", strtotime($snplist->created_at)),
                ];
            }
        }


        if(empty($final_non_posted_list)) {
            return $this->sendError('Do not have any posting data');
        } 
        $account_default_setting = AccountDefaultSetting::where('company_id', $company_id)->first();
        $sales_ids    = [];
        $payment_ids    = [];


        // Cash Sales
        $cash_in_hand_account_ledger  = getLedgerAccountById($account_default_setting->cash_in_hand_account);
        $cash_sales_account_ledger  = getLedgerAccountById($account_default_setting->cash_sales_account);
        $cash_posting_list = [];

        if($sales_type == "cash" || $sales_type == "all") {
            $old_cash_sale_date = '';
            $total_cash_amount = 0;
            if(array_key_exists('cash', $final_non_posted_list) && count($final_non_posted_list['cash']) > 0) {
                foreach($final_non_posted_list['cash'] as $cash_sale) {

                    $cash_sale  = (object) $cash_sale;
                        $new_cash_sale_date  = $cash_sale->sale_date;
                        //                    if($new_cash_sale_date == $old_cash_sale_date) {
                        //                        $total_cash_amount += $cash_sale->amount;
                        //                    }else{
                        //                        $total_cash_amount = $cash_sale->amount;
                        //                    }

                    $total_cash_amount  = collect($final_non_posted_list['cash'])->where('sale_date', $cash_sale->sale_date)->sum('amount');

                    $cash_posting_list[$cash_sale->sale_date]   = [
                        'name'  => 'Cash',
                        'type'  => 'cash',
                        'debit_ledger_code'     => $cash_in_hand_account_ledger->ledger_code,
                        'credit_ledger_code'    => $cash_sales_account_ledger->ledger_code,
                        'amount'    => $total_cash_amount,
                        'sale_date' => $cash_sale->sale_date,
                        'commission_percent'    => 0,
                        'charge_ledger_code'    => '',
                        'commission_amount'     => 0,
                    ];

                    // add sale ids
                    if(!in_array($cash_sale->sale_id, $sales_ids)) {
                        $sales_ids[] = $cash_sale->sale_id;
                    }

                    // add payment id based on sale id;
                    if(!array_key_exists($cash_sale->sale_id, $payment_ids)) {
                        $payment_ids[$cash_sale->sale_id] = [];
                    }
                    if(!in_array($cash_sale->payment_id, $payment_ids[$cash_sale->sale_id])) {
                        $payment_ids[$cash_sale->sale_id][] = $cash_sale->payment_id;
                    }

                    $old_cash_sale_date = $cash_sale->sale_date;
                }
            }
        }

        //Credit Card
        $default_bank_account_ledger  = getLedgerAccountById($account_default_setting->bank_account);
        $credit_card_sales_account_ledger  = getLedgerAccountById($account_default_setting->card_sales_account);
        $credit_card_charge_account_ledger  = getLedgerAccountById($account_default_setting->bank_charge_expense_account);

        $credit_card_posting_list = [];
        if($sales_type == "credit_card" || $sales_type == "all") {

            $old_credit_card_sale_date = '';
            $old_credit_card_bank_id = '';
            $total_credit_card_amount = 0;
            if(array_key_exists('credit_card', $final_non_posted_list) && count($final_non_posted_list['credit_card']) > 0){

                foreach($final_non_posted_list['credit_card'] as $credit_card_sale) {

                    $credit_card_sale  = (object) $credit_card_sale;
                    $new_credit_card_sale_date  = $credit_card_sale->sale_date;
                    $new_credit_card_bank_id  = $credit_card_sale->bank_id;

                    $bank_account = BankAccount::find($credit_card_sale->bank_id);
                    $bank_ledger_account    = $bank_account->bank_asset_accounts ?? '';
                    if($bank_account && $bank_ledger_account) {
                        $bank_ledger_code   = $bank_ledger_account->ledger_code;
                    }else{
                        $bank_ledger_code   = $default_bank_account_ledger->ledger_code;
                    }

                    //                    if(($new_credit_card_sale_date == $old_credit_card_sale_date) && ($new_credit_card_bank_id == $old_credit_card_bank_id)) {
                    //                        $total_credit_card_amount += $credit_card_sale->amount;
                    //                    }else{
                    //                        $total_credit_card_amount = $credit_card_sale->amount;
                    //                    }

                    $total_credit_card_amount   = collect($final_non_posted_list['credit_card'])->where('sale_date', $credit_card_sale->sale_date)->where('bank_id', $credit_card_sale->bank_id)->sum('amount');

                    $charge_percent = $bank_account ? $bank_account->charge_percent : 0;
                    $charge_amount  = ($total_credit_card_amount * $charge_percent) / 100;
                    $credit_card_posting_list[$credit_card_sale->sale_date.'__'.($credit_card_sale->bank_id ?? 0)]   = [
                        'name'  => 'Credit Card - '.($bank_account->account_no ?? 'N/A'),
                        'type'  => 'credit_card',
                        'debit_ledger_code'     => $bank_ledger_code,
                        'credit_ledger_code'    => $credit_card_sales_account_ledger->ledger_code,
                        'amount'    => $total_credit_card_amount,
                        'cutomer_ids'    => 'all customer ids',
                        'sale_date' => $credit_card_sale->sale_date,
                        'commission_percent'    => $charge_percent,
                        'charge_ledger_code'    => $credit_card_charge_account_ledger->ledger_code,
                        'commission_amount'     => $charge_amount,
                    ];


                    // add sale ids
                    if(!in_array($credit_card_sale->sale_id, $sales_ids)) {
                        $sales_ids[] = $credit_card_sale->sale_id;
                    }

                    // add payment id based on sale id;
                    if(!array_key_exists($credit_card_sale->sale_id, $payment_ids)) {
                        $payment_ids[$credit_card_sale->sale_id] = [];
                    }
                    if(!in_array($credit_card_sale->payment_id, $payment_ids[$credit_card_sale->sale_id])) {
                        $payment_ids[$credit_card_sale->sale_id][] = $credit_card_sale->payment_id;
                    }

                    $old_credit_card_sale_date = $credit_card_sale->sale_date;
                    $old_credit_card_bank_id = $credit_card_sale->bank_id;
                }

            }
        }


        // MFS Sales
        $default_mfs_bank_account_ledger    = getLedgerAccountById($account_default_setting->bank_reference_ledger_mfs);
        $default_mfs_sales_account_ledger   = getLedgerAccountById($account_default_setting->mfs_sales_account);
        $default_mfs_charge_account_ledger   = getLedgerAccountById($account_default_setting->mfs_charge_ledger);

        // Bkash Sales
        $bkash_mfs_bank_account_ledger  = getLedgerAccountById($account_default_setting->bank_reference_ledger_bkash);
        $bkash_sales_account_ledger = getLedgerAccountById($account_default_setting->bkash_sales_account);
        $bkash_charge_account_ledger    = getLedgerAccountById($account_default_setting->bkash_charge_ledger);

        $bkash_debit_ledger = $bkash_mfs_bank_account_ledger ? $bkash_mfs_bank_account_ledger : $default_mfs_bank_account_ledger;
        $bkash_credit_ledger    = $bkash_sales_account_ledger ? $bkash_sales_account_ledger : $default_mfs_sales_account_ledger;
        $bkash_charge_ledger = $bkash_charge_account_ledger ? $bkash_charge_account_ledger : $default_mfs_charge_account_ledger;

        $bkash_sale_posting_list    = [];
        if($sales_type == "bkash" || $sales_type == "all") {

            $old_bkash_sale_date    = '';
            $total_bkash_sale_amount = 0;
            if(array_key_exists('bkash', $final_non_posted_list) && count($final_non_posted_list['bkash']) > 0) {

                foreach ($final_non_posted_list['bkash'] as $bkash_sale) {
                    $bkash_sale = (object)$bkash_sale;
                    $new_bkash_sale_date = $bkash_sale->sale_date;
                    $bkash_wallet = MobileWallet::find($bkash_sale->wallet_id);


                    //                    if (($new_bkash_sale_date == $old_bkash_sale_date)) {
                    //                        $total_bkash_sale_amount += $bkash_sale->amount;
                    //                    } else {
                    //                        $total_bkash_sale_amount = $bkash_sale->amount;
                    //                    }

                    $total_bkash_sale_amount    = collect($final_non_posted_list['bkash'])->where('sale_date', $bkash_sale->sale_date)->sum('amount');
                    $charge_percent = $bkash_wallet ? $bkash_wallet->charge_percent : 0;
                    $charge_amount = ($total_bkash_sale_amount * $charge_percent) / 100;
                    $bkash_sale_posting_list[$bkash_sale->sale_date] = [
                        'name' => 'Bkash',
                        'type' => 'bkash',
                        'debit_ledger_code' => $bkash_debit_ledger->ledger_code,
                        'credit_ledger_code' => $bkash_credit_ledger->ledger_code,
                        'amount' => $total_bkash_sale_amount,
                        'sale_date' => $bkash_sale->sale_date,
                        'commission_percent' => $charge_percent,
                        'charge_ledger_code' => $bkash_charge_ledger->ledger_code,
                        'commission_amount' => $charge_amount,
                    ];


                    // add sale ids
                    if(!in_array($bkash_sale->sale_id, $sales_ids)) {
                        $sales_ids[] = $bkash_sale->sale_id;
                    }

                    // add payment id based on sale id;
                    if(!array_key_exists($bkash_sale->sale_id, $payment_ids)) {
                        $payment_ids[$bkash_sale->sale_id] = [];
                    }

                    if(!in_array($bkash_sale->payment_id, $payment_ids[$bkash_sale->sale_id])) {
                        $payment_ids[$bkash_sale->sale_id][] = $bkash_sale->payment_id;
                    }


                    $old_bkash_sale_date = $bkash_sale->sale_date;
                }
            }
        }

        // Nagad Sales
        $nagad_mfs_bank_account_ledger  = getLedgerAccountById($account_default_setting->bank_reference_ledger_nagad);
        $nagad_sales_account_ledger = getLedgerAccountById($account_default_setting->nagad_sales_account);
        $nagad_charge_account_ledger    = getLedgerAccountById($account_default_setting->nagad_charge_ledger);

        $nagad_debit_ledger = $nagad_mfs_bank_account_ledger ? $nagad_mfs_bank_account_ledger : $default_mfs_bank_account_ledger;
        $nagad_credit_ledger    = $nagad_sales_account_ledger ? $nagad_sales_account_ledger : $default_mfs_sales_account_ledger;
        $nagad_charge_ledger = $nagad_charge_account_ledger ? $nagad_charge_account_ledger : $default_mfs_charge_account_ledger;

        $nagad_sale_posting_list    = [];
        if($sales_type == "nagad" || $sales_type == "all") {
            $old_nagad_sale_date    = '';
            $total_nagad_sale_amount = 0;
            if(array_key_exists('nagad', $final_non_posted_list) && count($final_non_posted_list['nagad']) > 0) {

                foreach ($final_non_posted_list['nagad'] as $nagad_sale) {
                    $nagad_sale = (object) $nagad_sale;
                    $new_nagad_sale_date    = $nagad_sale->sale_date;
                    $nagad_wallet   = MobileWallet::find($nagad_sale->wallet_id);


                    //                    if(($new_nagad_sale_date == $old_nagad_sale_date)) {
                    //                        $total_nagad_sale_amount += $nagad_sale->amount;
                    //                    }else{
                    //                        $total_nagad_sale_amount = $nagad_sale->amount;
                    //                    }

                    $total_nagad_sale_amount = collect($final_non_posted_list['nagad'])->where('sale_date', $nagad_sale->sale_date)->sum('amount');

                    $charge_percent = $nagad_wallet ? $nagad_wallet->charge_percent : 0;
                    $charge_amount  = ($total_nagad_sale_amount * $charge_percent) / 100;
                    $nagad_sale_posting_list[$nagad_sale->sale_date]    = [
                        'name'  => 'Nagad',
                        'type'  => 'nagad',
                        'debit_ledger_code'     => $nagad_debit_ledger->ledger_code,
                        'credit_ledger_code'    => $nagad_credit_ledger->ledger_code,
                        'amount'    => $total_nagad_sale_amount,
                        'sale_date' => $nagad_sale->sale_date,
                        'commission_percent'    => $charge_percent,
                        'charge_ledger_code'    => $nagad_charge_ledger->ledger_code,
                        'commission_amount'     => $charge_amount,
                    ];


                    // add sale ids
                    if(!in_array($nagad_sale->sale_id, $sales_ids)) {
                        $sales_ids[] = $nagad_sale->sale_id;
                    }

                    // add payment id based on sale id;
                    if(!array_key_exists($nagad_sale->sale_id, $payment_ids)) {
                        $payment_ids[$nagad_sale->sale_id] = [];
                    }

                    if(!in_array($nagad_sale->payment_id, $payment_ids[$nagad_sale->sale_id])) {
                        $payment_ids[$nagad_sale->sale_id][] = $nagad_sale->payment_id;
                    }

                    $old_nagad_sale_date    = $nagad_sale->sale_date;
                }
            }
        }


        // Rocket Sales
        $rocket_mfs_bank_account_ledger  = getLedgerAccountById($account_default_setting->bank_reference_ledger_rocket);
        $rocket_sales_account_ledger = getLedgerAccountById($account_default_setting->rocket_sales_account);
        $rocket_charge_account_ledger    = getLedgerAccountById($account_default_setting->rocket_charge_ledger);

        $rocket_debit_ledger = $rocket_mfs_bank_account_ledger ? $rocket_mfs_bank_account_ledger : $default_mfs_bank_account_ledger;
        $rocket_credit_ledger    = $rocket_sales_account_ledger ? $rocket_sales_account_ledger : $default_mfs_sales_account_ledger;
        $rocket_charge_ledger = $rocket_charge_account_ledger ? $rocket_charge_account_ledger : $default_mfs_charge_account_ledger;

        $rocket_sale_posting_list    = [];
        if($sales_type == "rocket" || $sales_type == "all") {
            $old_rocket_sale_date    = '';
            $total_rocket_sale_amount = 0;
            if(array_key_exists('rocket', $final_non_posted_list) && count($final_non_posted_list['rocket']) > 0) {

                foreach ($final_non_posted_list['rocket'] as $rocket_sale) {
                    $rocket_sale = (object) $rocket_sale;
                    $new_rocket_sale_date    = $rocket_sale->sale_date;
                    $rocket_wallet   = MobileWallet::find($rocket_sale->wallet_id);


                    //                    if(($new_rocket_sale_date == $old_rocket_sale_date)) {
                    //                        $total_rocket_sale_amount += $rocket_sale->amount;
                    //                    }else{
                    //                        $total_rocket_sale_amount = $rocket_sale->amount;
                    //                    }
                    $total_rocket_sale_amount   = collect($final_non_posted_list['rocket'])->where('sale_date', $rocket_sale->sale_date)->sum('amount');

                    $charge_percent = $rocket_wallet ? $rocket_wallet->charge_percent : 0;
                    $charge_amount  = ($total_rocket_sale_amount * $charge_percent) / 100;
                    $rocket_sale_posting_list[$rocket_sale->sale_date]    = [
                        'name'  => 'Rocket',
                        'type'  => 'rocket',
                        'debit_ledger_code'     => $rocket_debit_ledger->ledger_code,
                        'credit_ledger_code'    => $rocket_credit_ledger->ledger_code,
                        'amount'    => $total_rocket_sale_amount,
                        'sale_date' => $rocket_sale->sale_date,
                        'commission_percent'    => $charge_percent,
                        'charge_ledger_code'    => $rocket_charge_ledger->ledger_code,
                        'commission_amount'     => $charge_amount,
                    ];


                    // add sale ids
                    if(!in_array($rocket_sale->sale_id, $sales_ids)) {
                        $sales_ids[] = $rocket_sale->sale_id;
                    }

                    // add payment id based on sale id;
                    if(!array_key_exists($rocket_sale->sale_id, $payment_ids)) {
                        $payment_ids[$rocket_sale->sale_id] = [];
                    }

                    if(!in_array($rocket_sale->payment_id, $payment_ids[$rocket_sale->sale_id])) {
                        $payment_ids[$rocket_sale->sale_id][] = $rocket_sale->payment_id;
                    }

                    $old_rocket_sale_date    = $rocket_sale->sale_date;
                }
            }
        }

        /** End MFS Sale */

        // Credit Sale
        $receivable_account_ledger  = getLedgerAccountById($account_default_setting->account_receivable_ledger);
        $credit_sales_account_ledger  = getLedgerAccountById($account_default_setting->credit_sales_account);


        $credit_sale_posting_list   = [];
        if($sales_type == "credit_sale" || $sales_type == "all") {
            $total_credit_sale_amount   = 0;
            $old_credit_sale_date   = '';
            $old_credit_sale_customer_id = '';
            if(array_key_exists('credit_sale', $final_non_posted_list) && count($final_non_posted_list['credit_sale']) > 0) {

                foreach ($final_non_posted_list['credit_sale'] as $credit_sale) {
                    $credit_sale = (object) $credit_sale;
                    $new_credit_sale_date    = $credit_sale->sale_date;
                    $new_credit_sale_customer_id    = $credit_sale->customer_id;

                    $customer   = Customer::find($credit_sale->customer_id);
                    $customer_ledger_account    = $customer->receivable_accounts;
                    if($customer && $customer_ledger_account) {
                        $receivable_ledger_code   = $customer_ledger_account->ledger_code;
                    }else{
                        $receivable_ledger_code   = $receivable_account_ledger->ledger_code;
                    }

                        //                    if(($new_credit_sale_date == $old_credit_sale_date) && ($new_credit_sale_customer_id == $old_credit_sale_customer_id)) {
                        //                        $total_credit_sale_amount += $credit_sale->amount;
                        //                    }else{
                        //                        $total_credit_sale_amount = $credit_sale->amount;
                        //                    }


                    $credit_sale_posting_list[$credit_sale->sale_date.'__'.($customer->id ?? 0)]    = [
                        'name'  => 'Credit Sale - '.($customer->customer_code ?? 'N/A'),
                        'type'  => 'credit_sale',
                        'debit_ledger_code'     => $receivable_ledger_code,
                        'credit_ledger_code'    => $credit_sales_account_ledger->ledger_code,
                        'amount'    => collect($final_non_posted_list['credit_sale'])->where('customer_id', $credit_sale->customer_id)->where('sale_date', $credit_sale->sale_date)->sum('amount'),
                        'sale_date' => $credit_sale->sale_date,
                        'commission_percent'    => 0,
                        'charge_ledger_code'    => '',
                        'commission_amount'     => 0,
                    ];


                    // add sale ids
                    if(!in_array($credit_sale->sale_id, $sales_ids)) {
                        $sales_ids[] = $credit_sale->sale_id;
                    }

                    // add payment id based on sale id;
                    $payment_ids[$credit_sale->sale_id] = [];

                    $old_credit_sale_date    = $credit_sale->sale_date;
                    $old_credit_sale_customer_id    = $credit_sale->customer_id;
                }
            }
        }


        // return [$cash_posting_list, $credit_card_posting_list, $bkash_sale_posting_list, $nagad_sale_posting_list, $rocket_sale_posting_list, $credit_sale_posting_list];

        $data = array_merge($cash_posting_list, $credit_card_posting_list, $bkash_sale_posting_list, $nagad_sale_posting_list, $rocket_sale_posting_list, $credit_sale_posting_list);

        $data_asc = array_merge($cash_posting_list, $credit_card_posting_list, $bkash_sale_posting_list, $nagad_sale_posting_list, $rocket_sale_posting_list, $credit_sale_posting_list);

        uasort($data, function($a, $b) {
            return strtotime($b['sale_date']) - strtotime($a['sale_date']);
        });

        $return_data    = [
            'sales_posting_data_desc'    => $data,
            'sales_posting_data_asc'    => $data_asc,
            'sales_ids' => $sales_ids,
            'payment_ids'   => $payment_ids,
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];

        return $this->sendResponse($return_data, 'Data retrieve successfully');

    }


    // Store Sale Account Posting List
    public function storeSaleAccountPosting(Request $request)
    {
        $company_id = checkCompanyIdByOutletId($request);          
        $fiscal_year = FiscalYear::where('status', 1)->where('company_id', $company_id)->first();
        $cost_center_id = CostCenter::where('company_id', $company_id)->first()->id;

        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $inputs = $request->all();
 
        $post_items = (array) json_decode($request->get('post_items'));
        $sales_ids = json_decode($request->get('sales_ids'));
        $payment_ids = (array) json_decode($request->get('payment_ids'));



        $sale_posted_ids    = [];
        $sale_partial_posted_ids = [];
        $sale_payment_posted_ids = [];

        $customer_data  = [];
        $old_sale_date  = '';
        $old_customer_id = '';
        $total_collection_amount = 0;
        $total_due_amount   = 0;
        if(count($sales_ids) > 0) {
            foreach ($sales_ids as $sales_id) {
                $sales_data = Sale::where('id', $sales_id)->first();
                $sale_date = date("Y-m-d", strtotime($sales_data->created_at));
                // For Customer Ledger Data
                $customer  = Customer::find($sales_data->customer_id);
                $new_sale_date  = $sale_date;
                $new_customer_id    = $customer->id;

                if($sales_data && ($sales_data->status == "paid" || $sales_data->status == "partial")) {
                    if($customer && (strtolower($customer->customer_group->title) != "walking customer")) {

                        if(($new_sale_date == $old_sale_date) && ($new_customer_id == $old_customer_id)){
                            $total_collection_amount    += round($sales_data->collection_amount);
                        }else{
                            $total_collection_amount = round($sales_data->collection_amount);
                        }
                        $customer_data[$sale_date.'__'.$customer->id] = [
                            'customer_id' => $sales_data->customer_id,
                            'type'  => 'payment_data',
                            'amount'    => $total_collection_amount,
                            'transaction_date'  => $sale_date,
                        ];
                    }
                }

                if($sales_data && ($sales_data->status == "due" || $sales_data->status == "partial")) {
                    if($customer && (strtolower($customer->customer_group->title) != "walking customer")) {

                        if(($new_sale_date == $old_sale_date) && ($new_customer_id == $old_customer_id)){
                            $total_due_amount    += round($sales_data->grand_total - $sales_data->collection_amount);
                        }else{
                            $total_due_amount = round($sales_data->grand_total - $sales_data->collection_amount);
                        }
                        $customer_data[$sale_date.'__'.$customer->id] = [
                            'customer_id' => $sales_data->customer_id,
                            'type'  => 'due_data',
                            'amount'    => $total_due_amount,
                            'transaction_date'  => $sale_date,
                        ];
                    }
                }

                $old_sale_date  = $sales_data->sale_date;
                $old_customer_id  = $customer->id;

                // End Customer Ledger Data


                // For Sale Status Update Data
                $sales_db_payment_count = $sales_data->payments ? $sales_data->payments()->count(): 0;
                $sales_generate_payment_count   = count($payment_ids[$sales_data->id]);
                if($sales_db_payment_count == $sales_generate_payment_count) {
                    $sale_posted_ids[]  = $sales_data->id;
                }else{
                    $sale_partial_posted_ids[]  = $sales_data->id;
                }

                if(count($payment_ids[$sales_data->id]) > 0) {
                    foreach ($payment_ids[$sales_data->id] as $pid) {
                        $sale_payment_posted_ids[]  = $pid;
                    }
                }

            }
        }



        // return [$post_items, $sales_ids, $payment_ids, $customer_data, $sale_posted_ids, $sale_partial_posted_ids, $sale_payment_posted_ids];

        DB::beginTransaction();
        try{

            if(count($post_items) > 0) {
                $entry_type = EntryType::where('label', 'journal')->where('company_id', $company_id)->first();

                foreach ($post_items as $post_item) {

                    $global_note = "";
                    if($post_item->type == 'credit_sale') {
                        $global_note = "Product Due Sale. Ref: ". $post_item->name;
                    }
                    if($post_item->type == 'credit_card') {
                        $global_note = "Product Credit Card sales. Ref: ". $post_item->name;
                    }
                    if($post_item->type == 'bkash') {
                        $global_note = "Product MFS Sales via Bkash";
                    }
                    if($post_item->type == 'nagad') {
                        $global_note = "Product MFS Sales via Nagad";
                    }
                    if($post_item->type == 'rocket') {
                        $global_note = "Product MFS Sales via Rocket";
                    }
                    if($post_item->type == 'cash') {
                        $global_note = "Product cash sales";
                    }


                    $transactions = [];
                    // Debit Ledger
                    if($post_item->debit_ledger_code != "") {
                        $debit_ledger   = AccountLedger::where('ledger_code', $post_item->debit_ledger_code)->where('company_id', $company_id)->first();

                        if($post_item->charge_ledger_code != "" && $post_item->commission_amount > 0){
                            $debit_amount = $post_item->amount - $post_item->commission_amount;
                        }else{
                            $debit_amount   = $post_item->amount;
                        }
                        $transactions[] = new AccountVoucherTransaction([
                            'company_id' => $company_id,
                            'cost_center_id' => $cost_center_id,
                            'vaccount_type' => 'dr',
                            'ledger_id' => $debit_ledger->id,
                            'ledger_code' => $post_item->debit_ledger_code,
                            'debit' => $debit_amount,
                            'credit' => 0,
                            'reference_id' => null,
                            'transaction_sl' => 1,
                            'voucher_note' => null,
                            'transaction_date' => $post_item->sale_date,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    // Charge Ledger
                    if($post_item->charge_ledger_code != "" && $post_item->commission_amount > 0) {

                        $charge_ledger   = AccountLedger::where('ledger_code', $post_item->charge_ledger_code)->first();

                        $transactions[] = new AccountVoucherTransaction([
                            'company_id' => $company_id,
                            'cost_center_id' => $cost_center_id,
                            'vaccount_type' => 'dr',
                            'ledger_id' => $charge_ledger->id,
                            'ledger_code' => $post_item->charge_ledger_code,
                            'debit' => $post_item->commission_amount,
                            'credit' => 0,
                            'reference_id' => $post_item->debit_ledger_code,
                            'transaction_sl' => 1,
                            'voucher_note' => null,
                            'transaction_date' => $post_item->sale_date,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    // Credit Ledger
                    if($post_item->credit_ledger_code != "") {

                        $credit_ledger   = AccountLedger::where('ledger_code', $post_item->credit_ledger_code)->first();

                        $transactions[] = new AccountVoucherTransaction([
                            'company_id' => $company_id,
                            'cost_center_id' => $cost_center_id,
                            'vaccount_type' => 'cr',
                            'ledger_id' => $credit_ledger->id,
                            'ledger_code' => $post_item->credit_ledger_code,
                            'debit' => 0,
                            'credit' => $post_item->amount,
                            'reference_id' => $post_item->debit_ledger_code,
                            'transaction_sl' => 1,
                            'voucher_note' => null,
                            'transaction_date' => $post_item->sale_date,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    $voucher_code   = $this->returnVoucherCode('journal');
                    $account_voucher_inputs  = [
                        'company_id' => $company_id,
                        'vcode' => $voucher_code,
                        'invoice_type'  => 'SALE',
                        'cost_center_id'    => 2,
                        'vtype_id'  => $entry_type->id,
                        'vtype_value'   => 'auto voucher',
                        'fiscal_year_id'    => $fiscal_year->id,
                        'vdate' => $post_item->sale_date,
                        'global_note'   => $global_note,
                        'modified_item' => 0,
                    ];


                    $voucher_save = AccountVoucher::create($account_voucher_inputs);

                    if(count($transactions) > 0) {
                        $transactions_save = $voucher_save->account_voucher_transactions()->saveMany($transactions);
                    }
                }

                if(count($sale_posted_ids) > 0) {
                    $sale_posted_update = Sale::whereIn('id', $sale_posted_ids)->update(['account_post_status' => 2]);
                }

                if(count($sale_partial_posted_ids) > 0) {
                    $sale_partial_posted_update = Sale::whereIn('id', $sale_partial_posted_ids)->update(['account_post_status' => 1]);
                }

                if(count($sale_payment_posted_ids) > 0) {
                    $sale_payment_posted_update = PaymentCollection::whereIn('id', $sale_payment_posted_ids)->update(['post_status' => 1]);
                }


                if(count($customer_data) > 0) {

                    foreach ($customer_data as $cledger) {

                        $cledger    = (object) $cledger;

                        $CustomerLedger = CustomerLedger::orderBy('id', 'DESC')->where('customer_id',$cledger->customer_id)->first();
                        if(empty($CustomerLedger)) {
                            $customer_closing_balance = 0;
                        } else {
                            $customer_closing_balance = $CustomerLedger->closing_balance;
                        }


                        $sales_amount = 0;
                        $payment_amount = 0;
                        $closing_balance    = $customer_closing_balance;
                        if($cledger->type == 'payment_data') {
                            $sales_amount = $cledger->amount;
                            $payment_amount = $cledger->amount;
                            $closing_balance    = $customer_closing_balance;
                        }

                        if($cledger->type == 'due_data') {
                            $sales_amount = $cledger->amount;
                            $payment_amount = 0;
                            $closing_balance    = $customer_closing_balance + $cledger->amount;
                        }

                        $customer_ledger_data = new CustomerLedger();
                        $customer_ledger_data->customer_id = $cledger->customer_id;
                        $customer_ledger_data->transaction_type = 'sale';
                        $customer_ledger_data->note = 'POS Sale';
                        $customer_ledger_data->sales_amount = $sales_amount;
                        $customer_ledger_data->payment_receive_amount = $payment_amount;
                        $customer_ledger_data->opening_balance = $customer_closing_balance;
                        $customer_ledger_data->closing_balance = $closing_balance;
                        $customer_ledger_data->transaction_date = $cledger->transaction_date;

                        $customer_ledger_data->save();
                    }
                }
            }

            DB::commit();
            return $this->sendSuccess("Sale Account posting successfully done!");

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }


    }
}


