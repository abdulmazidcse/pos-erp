<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\ProductExpiresDate;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\StockProduct;
use App\Models\StockProductsLog;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends AppBaseController
{
    public function getPurchaseReceiveInvoice(Request $request)
    {
        $supplier_id = $request->get('supplier_id');

        $purchase_receive_data  = PurchaseReceive::where('supplier_id', $supplier_id)->orderBy('id', 'desc')->get();

        $purchase_receive_items = [];
        if(count($purchase_receive_data) > 0) {
            foreach ($purchase_receive_data as $purchase_receive) {
                if($purchase_receive->purchase_receive_details) {
                    $purchase_receive_items[$purchase_receive->id] = $purchase_receive->purchase_receive_details;
                }
            }
        }

        $response = [
            'purchase_receives' => $purchase_receive_data,
//            'purchase_receive_items' => $purchase_receive_items
        ];

        return $this->sendResponse($response, 'Data retrieve successfully');

    }

    public function getPurchaseReceiveItems(Request $request)
    {
        $purchase_receive_id = $request->get('purchase_receive_id');
        $purchase_receive = PurchaseReceive::with(['purchase_receive_details'])->findOrFail($purchase_receive_id);

        if(empty($purchase_receive)) {
            return $this->sendError('Data not found');
        }

        $purchase_receive_items = [];
        if(count($purchase_receive->purchase_receive_details) > 0) {
            foreach($purchase_receive->purchase_receive_details as $receive_item) {

                $supplier_name  = ($receive_item->suppliers) ? $receive_item->suppliers->name : '';
                $product_code = ($receive_item->products) ? $receive_item->products->product_code : '';
                $product_name = ($receive_item->products) ? $receive_item->products->product_name : '';

                $isExpirable   = ($receive_item->products && ($receive_item->products->is_expirable == 1)) ? true : false;

                $unit_id    = ($receive_item->receive_product_unit_id != 0) ? $receive_item->receive_product_unit_id : $receive_item->products->purchase_measuring_unit;
                $unit_code  = ($receive_item->units) ? $receive_item->units->unit_code : 'pcs';

                $purchase_receive_items[] = [
                    'receive_id'            => $receive_item->id,
                    'supplier_id' => $receive_item->receive_supplier_id,
                    'supplier_name'   => $supplier_name,
                    'product_id'  => $receive_item->receive_product_id,
                    'product_code'    => $product_code,
                    'product_name'    => $product_name,
                    'is_expirable'  => $isExpirable,
                    'unit_code'   => $unit_code,
                    'product_unit_id' => $unit_id,
                    'purchase_qty'  => $receive_item->receive_quantity,
                    'expire_date'   => '',
                    'expire_qty'   => '',
                    'return_qty'  => '',
                    'tp'  => $receive_item->receive_purchase_price,
                    'mrp' => $receive_item->receive_mrp_price,
                ];
            }
        }
        return $this->sendResponse($purchase_receive_items, 'Data retrieve successfully!');
    }

    public function getProductExpireData(Request $request)
    {
        $item_id    = $request->get('item_id');

        $expires_data = ProductExpiresDate::where('purchase_receive_details_id', $item_id)
                                            ->orWhere('direct_pr_details_id', $item_id)
                                            ->get();

        return $this->sendResponse($expires_data, 'Data retrieve successfully!');
    }

    public function storePurchaseReturn(Request $request){
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;
        $this->validate($request, [
            'return_date' => 'required|date|after_or_equal:'.$start_date.'|before_or_equal:'.$end_date,
            'outlet_id' => 'required',
        ]);

        $authUser = auth('api')->user();
        $outlet_id  = $request->get('outlet_id');
        $user_id    = $authUser->id;
        $return_items   = json_decode($request->get('return_items'));
        $return_reference_no    = $this->returnPurchaseReturnReferenceNo($outlet_id, 'SR');

//        return $return_items;

        if(count($return_items) > 0) {
            $supplier_items_array = array();
            $item_return_array = array();
            $supplier_ids = array();
            $total_return_qty   = 0;
            $total_return_amount = 0;
            foreach($return_items as $item) {

                if(($item->tp != 0 && $item->tp != "") && ($item->return_qty != 0 && $item->return_qty != "")) {
                    if (!in_array($item->supplier_id, $supplier_ids)) {
                        $supplier_ids[] = $item->supplier_id;
                    }

                    $return_quantity = $item->return_qty;
                    $return_amount = ($item->tp * $item->return_qty);
                    $expire_date    = ($item->expire_date != "") ? date("Y-m-d", strtotime($item->expire_date)) : '';

                    $supplier_items_array[$item->supplier_id][]  = [
                        'product_stock_id'    => $item->product_stock_id,
                        'product_id'    => $item->product_id,
                        'product_unit_id'    => $item->measuring_unit,
                        'expire_date'   => $expire_date,
                        'return_purchase_price' => $item->tp,
                        'return_quantity'   => $return_quantity,
                        'return_amount' => $return_amount,
                        'note'  => $item->note,
                    ];

                    $item_return_array[]    = [
                        'product_stock_id'    => $item->product_stock_id,
                        'product_id'    => $item->product_id,
                        'product_unit_id'    => $item->measuring_unit,
                        'expire_date'   => $expire_date,
                        'return_purchase_price' => $item->tp,
                        'return_quantity'   => $return_quantity,
                        'return_amount' => $return_amount,
                        'note'  => $item->note,
                    ];

                    $total_return_qty   += $return_quantity;
                    $total_return_amount    += $return_amount;
                }

            }

            if(count($supplier_ids) > 0) {
                DB::beginTransaction();
                try {
                    $stock_log_inputs = array();
                    $supplier_return_items = array();
                    for($s=0; $s<count($supplier_ids); $s++) {
                        $supplier_id = $supplier_ids[$s];

                        $total_return_quantity = 0;
                        $total_return_amount = 0;
                        if(count($supplier_items_array[$supplier_id]) > 0) {
                            foreach($supplier_items_array[$supplier_id] as $supplier_item){
                                $supplier_item = (object) $supplier_item;

                                $total_return_quantity += $supplier_item->return_quantity;
                                $total_return_amount    += $supplier_item->return_amount;

                                $stock_data = StockProduct::where('id', $supplier_item->product_stock_id)->where('outlet_id', $outlet_id)->first();
                                $old_stock_quantity = $stock_data->stock_quantity;
                                $old_out_stock_quantity = $stock_data->out_stock_quantity;

                                $new_stock_quantity = $old_stock_quantity - $supplier_item->return_quantity;
                                $new_out_stock_quantity = $old_out_stock_quantity + $supplier_item->return_quantity;

                                // For Update Stock
                                $update_stock = [
                                    'stock_quantity'    => $new_stock_quantity,
                                    'out_stock_quantity'    => $new_out_stock_quantity
                                ];
                                $stock_data->update($update_stock);

                                // For Stock Log Insert
                                $stock_log_inputs[] = [
                                    'product_id'    => $supplier_item->product_id,
                                    'outlet_id'     => $outlet_id,
                                    'stock_quantity'=> $new_stock_quantity,
                                    'out_stock_quantity'    => $supplier_item->return_quantity,
                                    'expires_date'  => ($supplier_item->expire_date != "") ? $supplier_item->expire_date : NULL,
                                    'stock_type'    => 'RP',
                                    'note'          => $supplier_item->note,
                                    'user_id'       => $user_id,
                                    'created_at'    => date("Y-m-d H:i:s"),
                                    'updated_at'    => date("Y-m-d H:i:s"),
                                ];

                                // For Purchase Return Details
                                $supplier_return_items[$s][]    = new PurchaseReturnDetail([
                                    'product_stock_id'    => $supplier_item->product_stock_id,
                                    'product_id'            => $supplier_item->product_id,
                                    'product_unit_id'    => $supplier_item->product_unit_id,
                                    'return_expire_date'    => ($supplier_item->expire_date != "") ? $supplier_item->expire_date : NULL,
                                    'return_purchase_price' => $supplier_item->return_purchase_price,
                                    'return_quantity'       => $supplier_item->return_quantity,
                                    'return_amount'         => $supplier_item->return_amount,
                                    'note'                  => $supplier_item->note,
//                                        'created_at'    => date("Y-m-d H:i:s"),
//                                        'updated_at'    => date("Y-m-d H:i:s"),
                                ]);
                            }
                        }


                        // For Supplier Ledger
                        $supplier = Supplier::where('id', $supplier_id)->first();
                        $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->orderBy('id', 'DESC')->first();

                        if(empty($supplier_ledger)) {
                            $supplier_closing_balance = 0;
                        }else{
                            $supplier_closing_balance = $supplier_ledger->closing_balance;
                        }

                        $closing_balance = ($supplier_closing_balance - $total_return_amount);
                        $supplier_ledger_inputs = [
                            'supplier_id'   => $supplier_id,
                            'transaction_type'  => 'PRR',
                            'opening_balance'   => $supplier_closing_balance,
                            'return_amount'   => $total_return_amount,
                            'closing_balance'   => $closing_balance,
                            'transaction_date'  => date("Y-m-d"),
                        ];

                        // For Central Ledger
                        $specific_ledger = ($supplier->payable_accounts) ? $supplier : "";
                        $ledger_data    = getLedgerAccounts('purchase_return', $specific_ledger);

                        if(empty($ledger_data)) {
                            return $this->sendError("Please configure account settings purchase return transaction ledger!");
                        }
                        $transactions = [];
                        if(count($ledger_data) > 0) {
                            $t = 0;
                            foreach ($ledger_data as $key => $v) {
                                $account_ledger = AccountLedger::where('ledger_code', $key)->first();
                                if($v == 'D') {
                                    $debit_amount = $total_return_amount;
                                    $credit_amount = 0;
                                    $vaccount_type = 'dr';
                                }else{
                                    $credit_amount = $total_return_amount;
                                    $debit_amount = 0;
                                    $vaccount_type = 'cr';
                                }

                                if($t == 0) {
                                    $reference_id = NULL;
                                }else{
                                    $reference_id = array_keys($ledger_data)[0];
                                }

                                $transactions[] = new AccountVoucherTransaction([
                                    'cost_center_id' => 2, // Based on selected cost center
                                    'ledger_id' => $account_ledger->id,
                                    'ledger_code'   => $account_ledger->ledger_code,
                                    'vaccount_type' => $vaccount_type,
                                    'debit' => $debit_amount,
                                    'credit'    => $credit_amount,
                                    'reference_id' => $reference_id,
                                    'transaction_sl' => $s + 1,
                                    'voucher_note'  => 'Product Return supplier - reference no: '.$return_reference_no,
                                    'created_at'    => date("Y-m-d H:i:s"),
                                    'updated_at'    => date("Y-m-d H:i:s"),
                                ]);

                                $t++;
                            }
                        }

                        $entry_type = EntryType::where('label', 'journal')->first();
                        $voucher_inputs = [
                            'vcode' => $this->returnVoucherCode('journal'),
                            'vtype_id'  => $entry_type->id,
                            'cost_center_id'    => 2,
                            'invoice_type'  => 'PRR',
                            'vtype_value'   => 'auto voucher',
                            'fiscal_year_id' => $fiscal_year->id,
                            'vdate' => $request->get('return_date'),
                            'global_note'   => 'Return Supplier Product',
                            'modified_item' => 0,
                        ];
                        // Purchase Return Table data add
                        $purchase_return_inputs = [
                            'reference_no'  => $return_reference_no,
                            'supplier_id'   => $supplier_id,
                            'outlet_id'     => $outlet_id,
                            'total_return_quantity' => $total_return_quantity,
                            'total_return_amount' => $total_return_amount,
                            'return_date'   => $request->get('return_date'),
                            'user_id'   => $user_id,

                        ];

                        $purchase_return_save = PurchaseReturn::create($purchase_return_inputs);
                        $return_details_save = $purchase_return_save->purchase_return_details()->saveMany($supplier_return_items[$s]);

                        $voucher_inputs['invoice_reference'] = $purchase_return_save->id;
                        $voucher_save   = AccountVoucher::create($voucher_inputs);
                        $transaction_save   = $voucher_save->account_voucher_transactions()->saveMany($transactions);

                        $supplier_ledger_inputs['voucher_id']   = $voucher_save->id;
                        $supplier_ledger_save = SupplierLedger::create($supplier_ledger_inputs);



                    }

                    // Save Stock Logs
                    $stock_log_save = StockProductsLog::insert($stock_log_inputs);

                    DB::commit();
                    return $this->sendSuccess("Purchase return successfully done!");
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $e->getMessage();
                }
            }else{
                return $this->sendError('Return Item Not Found!');
            }

        }else{
            return $this->sendError('Return Item Not Found!');
        }
    }

//    public function storePurchaseReturn(Request $request){
//        $fiscal_year = FiscalYear::where('status', 1)->first();
//        $start_date = $fiscal_year->start_date;
//        $end_date   = $fiscal_year->end_date;
//        $this->validate($request, [
//            'return_date' => 'required|date|after_or_equal:'.$start_date.'|before_or_equal:'.$end_date,
//        ]);
//
//        $authUser = auth('api')->user();
//        $outlet_id  = ($authUser->outlet_id != 0) ? $authUser->outlet_id : 1;
//        $user_id    = $authUser->id;
//        $return_items   = json_decode($request->get('return_items'));
//
////        return $return_items;
//
//        if(count($return_items) > 0) {
//            $supplier_items_array = array();
//            $item_return_array = array();
//            $product_array  = array();
//            $supplier_ids = array();
//            $total_return_qty   = 0;
//            $total_return_amount = 0;
//            foreach($return_items as $item) {
//
//                if(($item->tp != 0 && $item->tp != "") && ($item->return_qty != 0 && $item->return_qty != "")) {
//                    if (!in_array($item->supplier_id, $supplier_ids)) {
//                        $supplier_ids[] = $item->supplier_id;
//                    }
//
//                    $return_quantity = $item->return_qty;
//                    $return_amount = ($item->tp * $item->return_qty);
//
//                    $supplier_items_array[$item->supplier_id][]  = [
//                        'product_stock_id'    => $item->product_stock_id,
//                        'product_id'    => $item->product_id,
//                        'expire_date'   => $item->expire_date,
//                        'return_purchase_price' => $item->tp,
//                        'return_quantity'   => $return_quantity,
//                        'return_amount' => $return_amount,
//                        'note'  => $item->note,
//                    ];
//
//                    $item_return_array[]    = [
//                        'product_stock_id'    => $item->product_stock_id,
//                        'product_id'    => $item->product_id,
//                        'expire_date'   => $item->expire_date,
//                        'return_purchase_price' => $item->tp,
//                        'return_quantity'   => $return_quantity,
//                        'return_amount' => $return_amount,
//                        'note'  => $item->note,
//                    ];
//
//                    $product_array[]    = [
//                        'product_id'  => $item->product_id,
//                        'is_expirable'  => $item->is_expirable,
//                        'product_unit_id' => $item->product_unit_id,
//                        'expire_date'   => $item->expire_date,
//                        'return_qty'  => $return_quantity,
//                    ];
//
//                    $total_return_qty   += $return_quantity;
//                    $total_return_amount    += $return_amount;
//                }
//
//            }
//
//            if(count($supplier_ids) > 0) {
//                DB::beginTransaction();
//                try {
//                    $stock_log_inputs = array();
//                    $supplier_return_items = array();
//                    for($s=0; $s<count($supplier_ids); $s++) {
//                        $supplier_id = $supplier_ids[$s];
//
//                        $total_return_quantity = 0;
//                        $total_return_amount = 0;
//                        if(count($supplier_items_array[$supplier_id]) > 0) {
//                            foreach($supplier_items_array[$supplier_id] as $supplier_item){
//                                $supplier_item = (object) $supplier_item;
//
//                                $total_return_quantity += $supplier_item->return_quantity;
//                                $total_return_amount    += $supplier_item->return_amount;
//
//                                if($supplier_item->is_expirable == true && $supplier_item->expire_date != "") {
//
//                                    $stock_data = StockProduct::where('product_id', $supplier_item->product_id)->where('expires_date', $supplier_item->expire_date)->where('outlet_id', $outlet_id)->first();
//                                    $old_stock_quantity = $stock_data->stock_quantity;
//                                    $old_out_stock_quantity = $stock_data->out_stock_quantity;
//
//                                    $new_stock_quantity = $old_stock_quantity - $supplier_item->return_quantity;
//                                    $new_out_stock_quantity = $old_out_stock_quantity + $supplier_item->return_quantity;
//
//                                    // Update Stock
//                                    $update_stock = [
//                                        'stock_quantity'    => $new_stock_quantity,
//                                        'out_stock_quantity'    => $new_out_stock_quantity
//                                    ];
//                                    $stock_data->update($update_stock);
//
//                                    // For Stock Log Insert
//                                    $stock_log_inputs[] = [
//                                        'product_id'    => $supplier_item->product_id,
//                                        'outlet_id'     => $outlet_id,
//                                        'stock_quantity'=> $new_stock_quantity,
//                                        'out_stock_quantity'    => $supplier_item->return_quantity,
//                                        'expires_date'  => $supplier_item->expire_date,
//                                        'stock_type'    => 'RP',
//                                        'note'          => $supplier_item->note,
//                                        'user_id'       => $user_id,
//                                        'created_at'    => date("Y-m-d H:i:s"),
//                                        'updated_at'    => date("Y-m-d H:i:s"),
//                                    ];
//
//                                    // For Purchase Return Details
//                                    $supplier_return_items[$s][]    = new PurchaseReturnDetail([
//                                        'receive_details_id'    => $supplier_item->receive_details_id,
//                                        'product_id'            => $supplier_item->product_id,
//                                        'product_unit_id'       => $supplier_item->product_unit_id,
//                                        'return_expire_date'    => $supplier_item->expire_date,
//                                        'return_purchase_price' => $supplier_item->return_purchase_price,
//                                        'return_quantity'       => $supplier_item->return_quantity,
//                                        'return_amount'         => $supplier_item->return_amount,
//                                        'note'                  => $supplier_item->note,
////                                        'created_at'            => date("Y-m-d H:i:s"),
////                                        'updated_at'            => date("Y-m-d H:i:s"),
//                                    ]);
//
//                                }else{
//                                    $stock_data = StockProduct::where('product_id', $supplier_item->product_id)->where('outlet_id', $outlet_id)->first();
//                                    $old_stock_quantity = $stock_data->stock_quantity;
//                                    $old_out_stock_quantity = $stock_data->out_stock_quantity;
//
//                                    $new_stock_quantity = $old_stock_quantity - $supplier_item->return_quantity;
//                                    $new_out_stock_quantity = $old_out_stock_quantity + $supplier_item->return_quantity;
//
//                                    // For Update Stock
//                                    $update_stock = [
//                                        'stock_quantity'    => $new_stock_quantity,
//                                        'out_stock_quantity'    => $new_out_stock_quantity
//                                    ];
//                                    $stock_data->update($update_stock);
//
//                                    // For Stock Log Insert
//                                    $stock_log_inputs[] = [
//                                        'product_id'    => $supplier_item->product_id,
//                                        'outlet_id'     => $outlet_id,
//                                        'stock_quantity'=> $new_stock_quantity,
//                                        'out_stock_quantity'    => $supplier_item->return_quantity,
//                                        'expires_date'  => NULL,
//                                        'stock_type'    => 'RP',
//                                        'note'          => $supplier_item->note,
//                                        'user_id'       => $user_id,
//                                        'created_at'    => date("Y-m-d H:i:s"),
//                                        'updated_at'    => date("Y-m-d H:i:s"),
//                                    ];
//
//                                    // For Purchase Return Details
//                                    $supplier_return_items[$s][]    = new PurchaseReturnDetail([
//                                        'receive_details_id'    => $supplier_item->receive_details_id,
//                                        'product_id'            => $supplier_item->product_id,
//                                        'product_unit_id'       => $supplier_item->product_unit_id,
//                                        'return_expire_date'    => NULL,
//                                        'return_purchase_price' => $supplier_item->return_purchase_price,
//                                        'return_quantity'       => $supplier_item->return_quantity,
//                                        'return_amount'         => $supplier_item->return_amount,
//                                        'note'                  => $supplier_item->note,
////                                        'created_at'    => date("Y-m-d H:i:s"),
////                                        'updated_at'    => date("Y-m-d H:i:s"),
//                                    ]);
//                                }
//                            }
//                        }
//
//
//                        // For Supplier Ledger
//                        $supplier = Supplier::where('id', $supplier_id)->first();
//                        $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->orderBy('id', 'DESC')->first();
//
//                        if(empty($supplier_ledger)) {
//                            $supplier_closing_balance = 0;
//                        }else{
//                            $supplier_closing_balance = $supplier_ledger->closing_balance;
//                        }
//
//                        $closing_balance = ($supplier_closing_balance - $total_return_amount);
//                        $supplier_ledger_inputs = [
//                            'supplier_id'   => $supplier_id,
//                            'transaction_type'  => 'PRR',
//                            'opening_balance'   => $supplier_closing_balance,
//                            'return_amount'   => $total_return_amount,
//                            'closing_balance'   => $closing_balance,
//                            'transaction_date'  => date("Y-m-d"),
//                        ];
//
//                        // For Central Ledger
//                        $specific_ledger = ($supplier->payable_accounts) ? $supplier : "";
//                        $ledger_data    = getLedgerAccounts('purchase_return', $specific_ledger);
//                        $transactions = [];
//                        if(count($ledger_data) > 0) {
//                            $t = 0;
//                            foreach ($ledger_data as $key => $v) {
//                                $account_ledger = AccountLedger::where('ledger_code', $key)->first();
//                                if($v == 'D') {
//                                    $debit_amount = $total_return_amount;
//                                    $credit_amount = 0;
//                                    $vaccount_type = 'dr';
//                                }else{
//                                    $credit_amount = $total_return_amount;
//                                    $debit_amount = 0;
//                                    $vaccount_type = 'cr';
//                                }
//
//                                if($t == 0) {
//                                    $reference_id = NULL;
//                                }else{
//                                    $reference_id = array_keys($ledger_data)[0];
//                                }
//
//                                $transactions[] = new AccountVoucherTransaction([
//                                    'cost_center_id' => 2, // Based on selected cost center
//                                    'ledger_id' => $account_ledger->id,
//                                    'ledger_code'   => $account_ledger->ledger_code,
//                                    'vaccount_type' => $vaccount_type,
//                                    'debit' => $debit_amount,
//                                    'credit'    => $credit_amount,
//                                    'reference_id' => $reference_id,
//                                    'transaction_sl' => $s + 1,
//                                    'created_at'    => date("Y-m-d H:i:s"),
//                                    'updated_at'    => date("Y-m-d H:i:s"),
//                                ]);
//
//                                $t++;
//                            }
//                        }
//
//                        $entry_type = EntryType::where('label', 'journal')->first();
//                        $voucher_inputs = [
//                            'vcode' => $this->returnVoucherCode('journal'),
//                            'vtype_id'  => $entry_type->id,
//                            'vtype_value'   => 'auto voucher',
//                            'fiscal_year_id' => $fiscal_year->id,
//                            'vdate' => $request->get('return_date'),
//                            'global_note'   => 'Return Supplier Product',
//                            'modified_item' => 0,
//                        ];
//                        // Purchase Return Table data add
//                        $purchase_return_inputs = [
//                            'reference_no'  => $this->returnPurchaseReturnReferenceNo($outlet_id, 'SR'),
//                            'supplier_id'   => $supplier_id,
//                            'outlet_id'     => $outlet_id,
//                            'total_return_quantity' => $total_return_quantity,
//                            'total_return_amount' => $total_return_amount,
//                            'return_date'   => $request->get('return_date'),
//                            'user_id'   => $user_id,
//
//                        ];
//
//                        $purchase_return_save = PurchaseReturn::create($purchase_return_inputs);
//                        $return_details_save = $purchase_return_save->purchase_return_details()->saveMany($supplier_return_items[$s]);
//
//                        $voucher_save   = AccountVoucher::create($voucher_inputs);
//                        $transaction_save   = $voucher_save->account_voucher_transactions()->saveMany($transactions);
//                        $supplier_ledger_inputs['voucher_id']   = $voucher_save->id;
//                        $supplier_ledger_save = SupplierLedger::create($supplier_ledger_inputs);
//
//
//
//                    }
//
//                    // Save Stock Logs
//                    $stock_log_save = StockProductsLog::insert($stock_log_inputs);
//
//                    DB::commit();
//                    return $this->sendSuccess("Purchase return successfully done!");
//                } catch (\Exception $e) {
//                    DB::rollBack();
//                    return $e->getMessage();
//                }
//            }else{
//                return $this->sendError('Return Item Not Found!');
//            }
//
//        }else{
//            return $this->sendError('Return Item Not Found!');
//        }
//    }





    // Get Purchase Return List
//    public function indexPurchaseReturn(Request $request)
//    {
//        $columns = ['id','reference_no', 'return_date', 'supplier_name', 'total_return_quantity', 'total_return_amount'];
//
//        $length = $request->input('length');
//        $column = $request->input('column');
//        $dir = $request->input('dir');
//        $searchValue = $request->input('search');
//
//        $query = Sale::select('id','created_at', 'invoice_number', 'total_amount', 'customer_name', 'collection_amount')->orderBy($columns[$column], $dir);
//
//        if($searchValue) {
//            $query->where(function ($query) use ($searchValue) {
//                $query->where('invoice_number', 'like', '%' .$searchValue. '%');
//                $query->orWhere('collection_amount', 'like', '%' .$searchValue. '%');
//                $query->orWhere('customer_name', 'like', '%' .$searchValue. '%');
//            });
//        }
//
//        $areas = $query->withCount('salesItems')->paginate($length);
//        $return_data    = [
//            'data' => $areas,
//            'draw' => $request->input('draw')
//        ];
//        return $this->sendResponse($return_data, 'Areas retrieved successfully');
//    }

    public function indexPurchaseReturn(Request $request)
    {
        $length = $request->input('length');
        $query = PurchaseReturn::filtered();

        $purchase_return_data = $query->paginate($length);
        $return_data    = [
            'data' => $purchase_return_data,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Purchase retrieved successfully');
    }

    public function showPurchaseReturn($id)
    {
        $purchase_return    = PurchaseReturn::with(['purchase_return_details'])->find($id);
        if(empty($purchase_return)) {
            return $this->sendError('Purchase Return Data Not Found!');
        }

        $prr_products = [];
        if(count($purchase_return->purchase_return_details) > 0) {
            foreach ($purchase_return->purchase_return_details as $prr_product) {

//                if(count($prr_products) > 0 && $prr_products['product_id'] == $prr_product->product_id && $prr_products['product_expire_date'] == $prr_product->return_expire_date ) {
                    $prr_products[] = [
                        'id' => $prr_product->id,
                        'purchase_return_id' => $prr_product->purchase_return_id,
                        'receive_details_id' => $prr_product->receive_details_id,
                        'product_id' => $prr_product->product_id,
                        'product_name' => $prr_product->products->product_name ?? '',
                        'product_code' => $prr_product->products->product_code ?? '',
                        'product_unit_id' => $prr_product->product_unit_id,
                        'product_unit_code' => $prr_product->units->unit_code,
                        'product_expire_date' => $prr_product->return_expire_date,
                        'return_purchase_price' => $prr_product->return_purchase_price,
                        'return_quantity' => $prr_product->return_quantity,
                        'note' => $prr_product->note,
                    ];
//                }else{
//                    $prr_products[] = [
//                        'id' => $prr_product->id,
//                        'purchase_return_id' => $prr_product->purchase_return_id,
//                        'receive_details_id' => $prr_product->receive_details_id,
//                        'product_id' => $prr_product->product_id,
//                        'product_name' => $prr_product->products->product_name,
//                        'product_code' => $prr_product->products->product_code,
//                        'product_unit_id' => $prr_product->product_unit_id,
//                        'product_unit_code' => $prr_product->units->unit_code,
//                        'product_expire_date' => $prr_product->return_expire_date,
//                        'return_purchase_price' => $prr_product->return_purchase_price,
//                        'return_quantity' => $prr_product->return_quantity,
//                        'note' => $prr_product->note,
//                    ];
//                }
            }
        }
        $return_data    = [
            'prr_data'    => [
                'id'      => $purchase_return->id,
                'reference_no'      => $purchase_return->reference_no,
                'supplier_id'      => $purchase_return->supplier_id,
                'supplier_name'     => $purchase_return->suppliers->name ?? "",
                'outlet_id'      => $purchase_return->outlet_id,
                'outlet_name'   => $purchase_return->outlets->name ?? "",
                'warehouse_id'      => $purchase_return->warehouse_id,
                'warehouse_name'      => $purchase_return->warehouses->name ?? "",
                'total_return_quantity'      => $purchase_return->total_return_quantity,
                'total_return_amount'      => $purchase_return->total_return_amount,
                'return_date'      => $purchase_return->return_date,
                'note'      => $purchase_return->note,
                'status'      => $purchase_return->status,
                'user_id'      => $purchase_return->user_id,
            ],
            'prr_products'  => $prr_products

        ];

        return $this->sendResponse($return_data, "Data Retrieve Successfully!");
    }

}
