<?php

namespace App\Imports;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductExpiresDate;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveDetail;
use App\Models\StockProduct;
use App\Models\StockProductsLog;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PurchaseReceiveProductImport extends AppBaseController implements
    ToCollection,
    WithHeadingRow,
    WithValidation
{
    use Importable;

    public $receiveProductData, $detailsProductData;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $this->receiveProductData = $rows;
        $auth_outlet_id = auth()->user()->outlet_id;
        $fiscal_year = FiscalYear::where('status', 1)->first();

        $product_expires_array = [];
        $stock_log_insert_inputs = [];
        $product_receive_details_inputs = [];
        $single_product_array   = [];
        $supplier_array = [];
        $supplier_product_array = [];
        $supplier_ledger_inputs = [];
        $account_transaction_array = [];
        $total_rcv_qty = 0;
        $total_rcv_value = 0;
        $total_free_amount = 0;

        // Collection Data Loop
        foreach ($rows as $row) {

            $outlet   = Outlet::where('name', $row['outlet_name'])->first();
            $supplier = Supplier::where('name', $row['supplier_name'])->first();
            $product = Product::where('product_name', $row['product_name'])->first();

            if(!empty($outlet) && !empty($supplier) && !empty($product)) {

                $new_order_qty  = $row['order_qty'] ?? 0;
                $new_receive_qty = $row['receive_qty'] ?? 0;
                $new_free_qty   = $row['free_qty']  ?? 0;
                $prd_tp = ($row['tp'] != 0 && $row['tp'] != "") ? $row['tp'] : $product->cost_price;
                $prd_mrp = ($row['mrp'] != 0 && $row['mrp'] != "") ? $row['mrp'] : $product->mrp_price;
                $new_lead_time = $row['lead_time'] ?? 0;

                $total_rcv_qty += $new_receive_qty;
                $total_rcv_value += ($new_receive_qty * $prd_tp);
                $total_free_amount += ($new_free_qty * $prd_tp);

                // For product receive details data
                if(!array_key_exists($product->id, $single_product_array)) {
                    $single_product_array[$product->id] = [
                        'receive_product_id' => $product->id,
                        'receive_supplier_id' => $supplier->id,
                        'receive_purchase_price' => $prd_tp,
                        'receive_mrp_price' => $prd_mrp,
                        'receive_order_quantity' => $new_order_qty,
                        'receive_quantity' => $new_receive_qty,
                        'receive_free_quantity' => $new_free_qty,
                        'receive_free_amount' => ($new_free_qty * $prd_tp),
                        'receive_product_value' => ($new_receive_qty * $prd_tp),
                        'receive_amount' => ($new_receive_qty * $prd_tp),
                    ];
                }else{
                    $old_rcv_qty = $single_product_array[$product->id]['receive_quantity'];
                    $old_free_qty = $single_product_array[$product->id]['receive_free_quantity'];
                    $old_free_amount = $single_product_array[$product->id]['receive_free_amount'];
                    $old_product_value = $single_product_array[$product->id]['receive_product_value'];
                    $old_rcv_amount = $single_product_array[$product->id]['receive_amount'];

                    $single_product_array[$product->id]['receive_quantity'] = ($old_rcv_qty + $new_receive_qty);
                    $single_product_array[$product->id]['receive_free_quantity'] = ($old_free_qty + $new_free_qty);
                    $single_product_array[$product->id]['receive_free_amount'] = ($old_free_amount + ($new_free_qty * $prd_tp));
                    $single_product_array[$product->id]['receive_product_value'] = ($old_product_value + ($new_receive_qty * $prd_tp));
                    $single_product_array[$product->id]['receive_amount'] = ($old_rcv_amount + ($new_receive_qty * $prd_tp));
                }

                // For Supplier Ledger Data Listing
                if(!in_array($supplier->id, $supplier_array)) {
                    $supplier_array[]   = $supplier->id;
                }

                $supplier_product_array[$supplier->id][] = [
                    'outlet_id'    => $outlet->id,
                    'product_id'    => $product->id,
                    'order_qty'     => $row['order_qty'],
                    'free_qty'      => $row['free_qty'],
                    'receive_qty'   => $row['receive_qty'],
                    'purchase_price' => $prd_tp,
                    'sale_price'    => $prd_mrp,
                ];

                // Purchase Receive with expiry date
                if(!empty($row['expiry_date'])) {
                    $expiry_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expiry_date']))->format("Y-m-d");
                    $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereDate('expires_date', $expiry_date)->first();

                    $old_stock = $stock_product_data->stock_quantity ?? 0;
                    if(!empty($stock_product_data)) {

                        $update_in_stock_qty = ($stock_product_data->in_stock_quantity + $new_receive_qty);
                        $update_stock_qty   = ($stock_product_data->stock_quantity + $new_receive_qty);

                        // for average tp
                        $old_stock_cost_price = $product->cost_price;
                        $old_stock_product_value = $old_stock * $old_stock_cost_price;
                        $receive_value  = $prd_tp * $new_receive_qty;

                        $average_tp = ($old_stock_product_value + $receive_value) / ($old_stock + $new_receive_qty);

                        $update_stock_inputs = [
                            'in_stock_quantity' => $update_in_stock_qty,
                            'stock_quantity'    => $update_stock_qty,
                            'lead_time' => $new_lead_time,

                        ];

                        $stock_update = $stock_product_data->update($update_stock_inputs);
                        $product_update = $product->update(['cost_price' => $average_tp]);

                    }else{
                        $stock_new_inputs = [
                            'product_id'    => $product->id,
                            'outlet_id'         => $outlet->id,
                            'in_stock_quantity' => $new_receive_qty,
                            'stock_quantity' => $new_receive_qty,
                            'expires_date' => $expiry_date,
                            'lead_time' => $new_lead_time,
                        ];

                        $stock_new_products = StockProduct::create($stock_new_inputs);

                        $product_update = $product->update(['cost_price' => $prd_tp]);
                    }

                    $stock_log_insert_inputs[] = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_receive_qty,
                        'stock_quantity'    => ($old_stock + $new_receive_qty),
                        'out_stock_quantity'    => 0,
                        'expires_date'  => $expiry_date,
                        'stock_type'    => 'PR',
                        'user_id'   => auth()->user()->id ?? 1,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];

                    $product_expires_array[$product->id][]    = [
                        'product_id'    => $product->id,
                        'expire_date'   => $expiry_date,
                        'expire_quantity'      => $new_receive_qty,
                        'created_at'    => Carbon::now()->toDateTimeString(),
                        'updated_at'    => Carbon::now()->toDateTimeString(),
                    ];


                }

                // Purchase Receive Without Expiry Date
                else{
                    $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->first();

                    $old_stock = $stock_product_data->stock_quantity ?? 0;
                    if(!empty($stock_product_data)) {

                        $update_in_stock_qty = ($stock_product_data->in_stock_quantity + $new_receive_qty);
                        $update_stock_qty   = ($stock_product_data->stock_quantity + $new_receive_qty);

                        // for average tp
                        $old_stock_cost_price = $product->cost_price;
                        $old_stock_product_value = $old_stock * $old_stock_cost_price;
                        $receive_value  = $prd_tp * $new_receive_qty;

                        $average_tp = ($old_stock_product_value + $receive_value) / ($old_stock + $new_receive_qty);

                        $update_stock_inputs = [
                            'in_stock_quantity' => $update_in_stock_qty,
                            'stock_quantity'    => $update_stock_qty,
                            'lead_time' => $new_lead_time,
                        ];

                        $stock_update = $stock_product_data->update($update_stock_inputs);
                        $product_update = $product->update(['cost_price' => $prd_tp]);

                    }else{
                        $stock_new_inputs = [
                            'product_id'    => $product->id,
                            'outlet_id'         => $outlet->id,
                            'in_stock_quantity' => $new_receive_qty,
                            'stock_quantity' => $new_receive_qty,
                        ];

                        $stock_new_products = StockProduct::create($stock_new_inputs);

                        $product_update = $product->update(['cost_price' => $prd_tp]);
                    }

                    $stock_log_insert_inputs[] = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_receive_qty,
                        'stock_quantity'    => ($old_stock + $new_receive_qty),
                        'out_stock_quantity'    => 0,
                        'expires_date'  => NULL,
                        'stock_type'    => 'PR',
                        'user_id'   => auth()->user()->id ?? 1,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }

                // Update Product cost and mrp_price
                $pupdate_input = [
//                                'cost_price'    => $product_array[$purchaseProducts->id]['receive_cost_price'],
                    'mrp_price'    => $prd_mrp,
                ];
                $product_update = $product->update($pupdate_input);

            }


        }

        // Array Setup for purchase receive details
        if(count($single_product_array) > 0) {
            foreach($single_product_array as $key => $value ) {
//                $product_receive_details_inputs[] = new PurchaseReceiveDetails($value);
                $product_receive_details_inputs[] = $value;
            }
        }

        // Array Setup for supplier ledger
        if(count($supplier_array) > 0) {
            for($i=0; $i<count($supplier_array); $i++) {
                $supplier_id    = $supplier_array[$i];

                $supplier_data  = Supplier::find($supplier_id);
                $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->orderBy('id', 'DESC')->first();
                if(empty($supplier_ledger)) {
                    $supplier_closing_balance = 0;
                }else{
                    $supplier_closing_balance = $supplier_ledger->closing_balance;
                }

                $net_total_amount = 0;
                $rcv_free_amount = 0;
                if(count($supplier_product_array[$supplier_id]) > 0){
                    for($j=0; $j<count($supplier_product_array[$supplier_id]); $j++) {
                        $product_qty    = $supplier_product_array[$supplier_id][$j]['receive_qty'];
                        $product_purchase_price = $supplier_product_array[$supplier_id][$j]['purchase_price'];
                        $product_free_qty = $supplier_product_array[$supplier_id][$j]['free_qty'];

                        $product_rcv_value = ($product_qty * $product_purchase_price);
                        $product_free_value = ($product_free_qty * $product_purchase_price);

                        $net_total_amount += $product_rcv_value;
                        $rcv_free_amount += $product_free_value;
                    }
                }


                $closing_balance = ($supplier_closing_balance + ($net_total_amount - $rcv_free_amount));
                $supplier_ledger_inputs[] = [
                    'supplier_id'   => $supplier_id,
                    'transaction_type'  => 'PR',
                    'opening_balance'   => $supplier_closing_balance,
                    'purchase_receive_amount'   => ($net_total_amount - $rcv_free_amount),
                    'closing_balance'   => $closing_balance,
                    'transaction_date'  => date("Y-m-d"),
                    'created_at'    => date("Y-m-d H:i:s"),
                    'updated_at'    => date("Y-m-d H:i:s"),
                ];


                // Account Transaction Data
                // For Central Ledger
                $specific_ledger = ($supplier_data->payable_accounts) ? $supplier_data : "";
                $ledger_data    = getLedgerAccounts('grn', $specific_ledger);

                if(empty($ledger_data)) {
                    return $this->sendError('Please configure settings purchase transaction ledger');
                }

                if(count($ledger_data) > 0) {
                    $t = 0;
                    foreach ($ledger_data as $key => $v) {
                        $account_ledger = AccountLedger::where('ledger_code', $key)->first();
                        if($v == 'D') {
                            $debit_amount = $net_total_amount;
                            $credit_amount = 0;
                            $vaccount_type = 'dr';
                        }else{
                            $credit_amount = $net_total_amount;
                            $debit_amount = 0;
                            $vaccount_type = 'cr';
                        }

                        if($t == 0) {
                            $reference_id = NULL;
                        }else{
                            $reference_id = array_keys($ledger_data)[0];
                        }

                        $account_transaction_array[] = new AccountVoucherTransaction([
                            'cost_center_id' => 2, // Based on selected cost center
                            'ledger_id' => $account_ledger->id,
                            'ledger_code'   => $account_ledger->ledger_code,
                            'vaccount_type' => $vaccount_type,
                            'debit' => $debit_amount,
                            'credit'    => $credit_amount,
                            'reference_id'  => $reference_id,
                            'transaction_sl'    => $i + 1,
                            'voucher_note'  => 'Purchase product receive from supplier - challan no: '.$this->returnPurchaseReceiveReferenceNo("SR", $auth_outlet_id),
                            'transaction_date'    => date("Y-m-d"),
                            'created_at'    => date("Y-m-d H:i:s"),
                            'updated_at'    => date("Y-m-d H:i:s"),
                        ]);

                        $t++;
                    }
                }
            }
        }

        // Test Data
        $this->detailsProductData = [
            'single_product_array' => $product_receive_details_inputs,
            'suppliers' => $supplier_array,
            'supplier_products' => $supplier_product_array
        ];

        // Array For purchase receive master data
        $receive_inputs = [
            'purchase_order_id' => 0,
            'supplier_id'       => 0,
            'receive_type'      => 'SR',
            'reference_no'      => $this->returnPurchaseReceiveReferenceNo("SR", $auth_outlet_id),
            //'challan_no'      => '',
            'purchase_date'     => date("Y-m-d"),
            'outlet_id'         => $auth_outlet_id,
            'delivery_to'       => $auth_outlet_id,
            'total_rcv_quantity' => $total_rcv_qty,
            'total_rcv_value'   => $total_rcv_value,
            'total_commission_value' => 0,
            'total_vat'     => 0,
            'total_free_amount' => $total_free_amount,
            'total_amount'  => $total_rcv_value,
            'additional_discount'   => 0,
            'additional_cost'   => 0,
            'net_amount'    => $total_rcv_value
        ];

        // Save All Table Data
        $purchase_receive_save = PurchaseReceive::create($receive_inputs);
        if(count($product_receive_details_inputs) > 0) {

            for($pr=0; $pr<count($product_receive_details_inputs); $pr++) {
                $ex_product_id  = $product_receive_details_inputs[$pr]['receive_product_id'];
                $receive_details_save = $purchase_receive_save->purchase_receive_details()->create($product_receive_details_inputs[$pr]);
                if(array_key_exists($ex_product_id, $product_expires_array) && count($product_expires_array[$ex_product_id]) > 0) {
                    for($pex=0; $pex<count($product_expires_array[$ex_product_id]); $pex++) {
                        $pex_insert_inputs    = [
                            'direct_pr_details_id'    => $receive_details_save->id,
                            'product_id'    => $product_expires_array[$ex_product_id][$pex]['product_id'],
                            'expire_date'   => $product_expires_array[$ex_product_id][$pex]['expire_date'],
                            'expire_quantity'      => $product_expires_array[$ex_product_id][$pex]['expire_quantity'],
                            'created_at'    => Carbon::now()->toDateTimeString(),
                            'updated_at'    => Carbon::now()->toDateTimeString(),
                        ];

                        $product_expires_date_save = ProductExpiresDate::create($pex_insert_inputs);
                    }
                }


            }

        }
        if(count($stock_log_insert_inputs)) {
            $stock_logs_save = StockProductsLog::insert($stock_log_insert_inputs);
        }

        if(count($supplier_ledger_inputs) > 0) {
            $supplier_ledger_save = SupplierLedger::insert($supplier_ledger_inputs);
        }


        if(count($account_transaction_array) > 0) {
            $entry_type = EntryType::where('label', 'journal')->first();
            $voucher_inputs = [
                'vcode' => $this->returnVoucherCode('journal'),
                'vtype_id'  => $entry_type->id,
                'cost_center_id'  => 2,
                'invoice_type'  => 'PR',
                'vtype_value'   => 'auto voucher',
                'fiscal_year_id' => $fiscal_year->id,
                'vdate' => date("Y-m-d"),
                'global_note'   => 'Purchase product receive from supplier',
                'modified_item' => 0,
            ];


            $voucher_save   = AccountVoucher::create($voucher_inputs);
            $transaction_save = $voucher_save->account_voucher_transactions()->saveMany($account_transaction_array);

        }

    }

    public function rules(): array
    {
        return [
            '*.outlet_name'   => 'required',
            '*.supplier_name'   => 'required',
            '*.product_name'   => 'required',
            '*.receive_qty'   => 'required',
        ];
    }
}
