<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\DamageProduct;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\MobileWallet;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class COGSAccountPostingAPIController extends AppBaseController
{
    public function COGSAccountPostingList(Request $request)
    {
        $this->validate($request, [
            'outlet_id'  => 'required',
            'posting_type'  => 'required',
            'from_date'   => 'required',
            'to_date'   => 'required',
        ]);

        $outlet_id  = $request->input('outlet_id');
        $posting_type = $request->input('posting_type');
        $from_date  = $request->input('from_date');
        $to_date    = $request->input('to_date');


        $query   = Sale::with(['payments', 'salesItems'])->where('outlet_id', $outlet_id)
                                        ->where('inventory_post_status', 0)
                                        ->whereDate('created_at', '>=', $from_date)
                                        ->whereDate('created_at', '<=', $to_date);



        $sale_non_posted_list   = $query->get();

        $query_damage_product   = DamageProduct::where('outlet_id', $outlet_id)
                                                ->where('post_status', 0)
                                                ->whereDate('created_at', '>=', $from_date)
                                                ->whereDate('created_at', '<=', $to_date);


        $damage_product_non_posted_list = $query_damage_product->get();
//        $damage_product_non_posted_list = [];

        if(empty($sale_non_posted_list) && empty($damage_product_non_posted_list)) {
            return $this->sendError("COGS Data not found!");
        }


        $account_default_setting    = AccountDefaultSetting::first();

        $inventory_account_ledger  = getLedgerAccountById($account_default_setting->inventory_account);
        $cogs_account_ledger  = getLedgerAccountById($account_default_setting->cogs_account);
        $inventory_damage_account_ledger  = getLedgerAccountById($account_default_setting->inventory_damage_account);

        // COGS Sales
        $final_sale_non_posted_list  = [];
        $final_damage_non_posted_list  = [];
        $item_value_array = [];
        $sale_ids   = [];
        $total_cogs_sale_amount = 0;
        $old_cogs_sale_date = '';
        if($posting_type == "cogs_sale" || $posting_type == "all") {
            foreach ($sale_non_posted_list as $snplist) {
                $sale_date = date("Y-m-d", strtotime($snplist->created_at));
                $new_cogs_sale_date = $sale_date;
                $total_item_value = 0;
                foreach ($snplist->salesItems as $sitem) {
                    $item_value = $sitem->cost_price * $sitem->quantity;
                    $item_value_array[] = [$sitem->cost_price, $item_value];
                    $total_item_value += $item_value;
                }

                if($new_cogs_sale_date == $old_cogs_sale_date) {
                    $total_cogs_sale_amount  += $total_item_value;
                }else{
                    $total_cogs_sale_amount  = $total_item_value;
                }

                $final_sale_non_posted_list[$sale_date] = [
                    'name'  => 'COGS-Sale',
                    'type'  => 'cogs_sale',
                    'debit_ledger_code'     => $cogs_account_ledger->ledger_code,
                    'credit_ledger_code'    => $inventory_account_ledger->ledger_code,
                    'amount'    => number_format($total_cogs_sale_amount, 6, '.', ''),
                    'posting_date' => $sale_date,
                ];

                $sale_ids[] = $snplist->id;


                $old_cogs_sale_date  = $sale_date;
            }
        }

//        return $item_value_array;

        // COGS Damage Product
        $damage_ids = [];
        $total_damage_product_amount = 0;
        $old_damage_product_date    = '';
        if($posting_type == "damage_product" || $posting_type == 'all') {

            if($inventory_damage_account_ledger) {
                $debit_ledger_code  = $inventory_damage_account_ledger->ledger_code;
            }else{
                $debit_ledger_code  = $cogs_account_ledger->ledger_code;
            }
            foreach ($damage_product_non_posted_list as $dproduct) {

                $dp_date    = date("Y-m-d", strtotime($dproduct->created_at));
                $new_damage_product_date    = $dp_date;

                $damage_amount  = ($dproduct->cost_price * $dproduct->damage_quantity);
                if($new_damage_product_date == $old_damage_product_date) {
                    $total_damage_product_amount += $damage_amount;
                }else{
                    $total_damage_product_amount = $damage_amount;
                }

                $final_damage_non_posted_list[$dp_date] = [
                    'name'  => 'COGS Damage',
                    'type'  => 'damage_product',
                    'debit_ledger_code'     => $debit_ledger_code,
                    'credit_ledger_code'    => $inventory_account_ledger->ledger_code,
                    'amount'    => number_format($total_damage_product_amount, 4, '.', ''),
                    'posting_date' => $dp_date,
                ];

                $damage_ids[]   = $dproduct->id;

                $old_damage_product_date    = $dp_date;
            }
        }

        //return [count($final_sale_non_posted_list), count($final_damage_non_posted_list)];

        if(count($final_sale_non_posted_list) == 0 && count($final_damage_non_posted_list) == 0) {
            return $this->sendError('Do not have any posting data');
        }



        $data_desc = array_merge($final_sale_non_posted_list, $final_damage_non_posted_list);
        $data_asc = array_merge($final_sale_non_posted_list, $final_damage_non_posted_list);

        uasort($data_desc, function($a, $b) {
            return strtotime($b['posting_date']) - strtotime($a['posting_date']);
        });


        $return_data    = [
            'cogs_posting_data_desc' => $data_desc,
            'cogs_posting_data_asc' => $data_asc,
            'sale_ids'  => $sale_ids,
            'damage_ids'    => $damage_ids,
            'from_date' => $from_date,
            'to_date'   => $to_date
        ];

        return $this->sendResponse($return_data, 'Data retrieve successfully');

    }


    // Store COGS and Damage Account Posting List
    public function storeCOGSAccountPosting(Request $request)
    {

        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $inputs = $request->all();
//
        $post_items = (array) json_decode($request->get('post_items'));
        $sales_ids = json_decode($request->get('sales_ids'));
        $damage_ids = json_decode($request->get('damage_ids'));


        DB::beginTransaction();
        try{

            if(count($post_items) > 0) {
                $entry_type = EntryType::where('label', 'journal')->first();

                foreach ($post_items as $post_item) {

                    $global_note = "";
                    $invoice_type = "";
                    if($post_item->type == 'cogs_sale') {
                        $global_note = "Product sales customer";
                        $invoice_type   = "SALE";
                    }
                    if($post_item->type == 'damage_product') {
                        $global_note = "Damage product from stock";
                        $invoice_type   = "DAMAGE";
                    }


                    $transactions = [];
                    // Debit Ledger
                    if($post_item->debit_ledger_code != "") {
                        $debit_ledger   = AccountLedger::where('ledger_code', $post_item->debit_ledger_code)->first();

                        $debit_amount   = $post_item->amount;

                        $transactions[] = new AccountVoucherTransaction([
                            'cost_center_id' => 2,
                            'vaccount_type' => 'dr',
                            'ledger_id' => $debit_ledger->id,
                            'ledger_code' => $post_item->debit_ledger_code,
                            'debit' => $debit_amount,
                            'credit' => 0,
                            'reference_id' => null,
                            'transaction_sl' => 1,
                            'voucher_note' => null,
                            'transaction_date' => $post_item->posting_date,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    // Credit Ledger
                    if($post_item->credit_ledger_code != "") {

                        $credit_ledger   = AccountLedger::where('ledger_code', $post_item->credit_ledger_code)->first();

                        $transactions[] = new AccountVoucherTransaction([
                            'cost_center_id' => 2,
                            'vaccount_type' => 'cr',
                            'ledger_id' => $credit_ledger->id,
                            'ledger_code' => $post_item->credit_ledger_code,
                            'debit' => 0,
                            'credit' => $post_item->amount,
                            'reference_id' => $post_item->debit_ledger_code,
                            'transaction_sl' => 1,
                            'voucher_note' => null,
                            'transaction_date' => $post_item->posting_date,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    $voucher_code   = $this->returnVoucherCode('journal');
                    $account_voucher_inputs  = [
                        'vcode' => $voucher_code,
                        'invoice_type'  => $invoice_type,
                        'cost_center_id'    => 2,
                        'vtype_id'  => $entry_type->id,
                        'vtype_value'   => 'auto voucher',
                        'fiscal_year_id'    => $fiscal_year->id,
                        'vdate' => $post_item->posting_date,
                        'global_note'   => $global_note,
                        'modified_item' => 0,
                    ];


                    $voucher_save = AccountVoucher::create($account_voucher_inputs);

                    if(count($transactions) > 0) {
                        $transactions_save = $voucher_save->account_voucher_transactions()->saveMany($transactions);
                    }
                }

                if(count($sales_ids) > 0) {
                    $sale_posted_update = Sale::whereIn('id', $sales_ids)->update(['inventory_post_status' => 1]);
                }

                if(count($damage_ids) > 0) {
                    $damage_posted_update = DamageProduct::whereIn('id', $damage_ids)->update(['post_status' => 1]);
                }

            }

            DB::commit();
            return $this->sendSuccess("Account posting successfully done!");

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }


    }
}
