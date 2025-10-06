<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\OrderRequisitionDetail;
use App\Models\Product;
use App\Models\ProductExpiresDate;
use App\Models\ProductGift;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\WarehouseStockProduct;
use App\Models\WarehouseStockProductGift;
use App\Models\WarehouseStockProductLog;
use App\Repositories\PurchaseReceiveRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

class WarehousePurchaseReceiveAPIController extends AppBaseController
{
    /** @var  PurchaseReceiveRepository */
    private $purchaseReceiveRepository;

    public function __construct(PurchaseReceiveRepository $purchaseReceiveRepo)
    {
        $this->purchaseReceiveRepository = $purchaseReceiveRepo;
    }

    /**
     * Display a listing of the PurchaseReceive.
     * GET|HEAD /purchaseReceives
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

    }

    /**
     * Store a newly created WarehousePurchaseReceive in storage.
     * POST /warehousePurchaseReceives
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        // response()->json($request->all());

        $this->validate($request, [
            'supplier_id' => 'required',
            'purchase_order_id' => 'required',
            'purchase_date' => 'required|date|after_or_equal:'.$start_date.'|before_or_equal:'.$end_date,
            'reference_no'  => 'required',
            'challan_no'    => 'sometimes',
            'warehouse_id'     => 'required|not_in:0',
        ]);

        //return response()->json($request->all());
        // For Without purchase order to receive product
        if($request->purchase_order_id == "direct") {
            //$outlet_id = auth()->user()->outlet_id ?? 1;
            $warehouse_id = $request->get('warehouse_id');

            $products = json_decode($request->get('products'));

            $product_array = [];
            $receive_details_product_array = [];
            $stock_log_inputs = [];
            $product_ids = [];
            $product_expires_data = [];
            $product_gifts_data = [];
            $total_rcv_quantity = 0;
            $total_rcv_weight = 0;
            $total_rcv_product_value = 0;
            $total_free_amount = 0;
            $total_discount_amount = 0;
            $total_rcv_amount = 0;
            $total_vat_amount = 0;
            if(count($products) > 0)
            {
                foreach ($products as $product) {

                    return $product->sequences;

                    //if($product->id != "" && ($product->purchase_price != 0 && $product->purchase_price != "") && ($product->sale_price != 0 && $product->sale_price != "") && ($product->rcv_qty != 0 && $product->rcv_qty != "")) {
                    if($product->id != "" && $product->purchase_price >= 0  && ($product->sale_price != 0 && $product->sale_price != "") && (($product->rcv_qty != 0 && $product->rcv_qty != "")) || ($product->rcv_weight != 0 && $product->rcv_weight != "")) {

                        if($product->unit_code == 'kg') {
                            $product_amount = $product->rcv_weight * $product->purchase_price;
                        }else {
                            $product_amount = $product->rcv_qty * $product->purchase_price;
                        }
                        $free_amount = $product->free_qty * $product->purchase_price;

                        $product_rcv_amount = ($product_amount - $product->disc_amount) + 0;

                        $product_ids[]  = $product->id;
                            //                        $product_array[$product->id]    = [
                            //                            'receive_purchase_price'    => $product->purchase_price,
                            //                            'receive_supplier_id'       => $request->get('supplier_id'),
                            //                            'receive_mrp_price'         => $product->sale_price,
                            //                            'receive_quantity'          => $product->rcv_qty,
                            //                            'receive_discount_percent'  => $product->disc_percent,
                            //                            'receive_discount_amount'   => $product->disc_amount,
                            //                            'receive_free_quantity'     => $product->free_qty,
                            //                            'receive_free_amount'       => $free_amount,
                            //                            'receive_product_value'     => $product_amount,
                            //                            'receive_vat_amount'        => 0,
                            //                            'receive_amount'            => $product_rcv_amount,
                            //                            'receive_status'            => 2,
                            //                        ];
                        $product_array[$product->id]    = [
                            'receive_quantity'          => $product->rcv_qty,
                            'receive_weight'            => $product->rcv_weight,
                            'receive_cost_price'        => $product->purchase_price,
                            'receive_mrp_price'         => $product->sale_price,
                        ];

                        // $receive_details_product_array[]   = new PurchaseReceiveDetails([
                        $receive_details_product_array[]   = [
                            'receive_product_id'        => $product->id,
                            'receive_product_unit_id'   => $product->product_unit_id,
                            'receive_supplier_id'       => $request->get('supplier_id'),
                            'receive_purchase_price'    => $product->purchase_price,
                            'receive_mrp_price'         => $product->sale_price,
                            'receive_order_quantity'    => $product->ord_qty,
                            'receive_quantity'          => $product->rcv_qty,
                            'receive_weight'            => $product->rcv_weight,
                            'receive_discount_percent'  => $product->disc_percent,
                            'receive_discount_amount'   => $product->disc_amount,
                            'receive_free_quantity'     => $product->free_qty,
                            'receive_free_amount'       => $free_amount,
                            'receive_product_value'     => $product_amount,
                            'receive_vat_amount'        => 0,
                            'receive_amount'            => $product_rcv_amount,
                        ];


                        $total_rcv_quantity += $product->rcv_qty;
                        $total_rcv_weight += $product->rcv_weight;
                        $total_rcv_product_value    += $product_amount;
                        $total_free_amount  += $free_amount;
                        $total_discount_amount  += $product->disc_amount;
                        $total_vat_amount   += 0;
                        $total_rcv_amount   += $product_rcv_amount;

                        $product_expires_data[$product->id] = [];
                        if($product->is_expirable && $product->is_expires && count($product->expires_data) > 0) {
                            foreach ($product->expires_data as $expires_data) {
                                if($expires_data->expire_date != "" && ($expires_data->expire_qty != 0 && $expires_data->expire_qty != "")) {
                                    $product_expires_data[$product->id][] = [
                                        'product_id' => $product->id,
                                        'expire_date' => $expires_data->expire_date,
                                        'expire_quantity' => $expires_data->expire_qty,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }
                            }
                        }

                        $product_gifts_data[$product->id] = [];
                        if($product->is_gifts && count($product->gifts) > 0) {
                            foreach ($product->gifts as $gift) {
                                if($gift->gift_name != "" && ($gift->gift_qty != 0 && $gift->gift_qty != "")) {
                                    $product_gifts_data[$product->id][] = [
                                        'product_id' => $product->id,
                                        'gift_name' => $gift->gift_name,
                                        'gift_quantity' => $gift->gift_qty,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }
                            }
                        }
                    }

                    
                    
                }
            }

            $net_total_amount = ($total_rcv_amount - $request->get('additional_discount')) + $request->get('additional_cost');

            $receive_inputs = [
                'purchase_order_id' => $request->get('purchase_order_id'),
                'supplier_id'       => $request->get('supplier_id'),
                'receive_type'      => 'WR',
                'reference_no'      => $request->get('reference_no'),
                'challan_no'      => $request->get('challan_no'),
                'purchase_date'     => $request->get('purchase_date'),
                'warehouse_id'         => $warehouse_id,
                'delivery_to'       => 0,
                'total_rcv_quantity' => $total_rcv_quantity,
                'total_rcv_weight' => $total_rcv_weight,
                'total_rcv_value'   => $total_rcv_product_value,
                'total_commission_value' => $total_discount_amount,
                'total_vat'     => $total_vat_amount,
                'total_free_amount' => $total_free_amount,
                'total_amount'  => $total_rcv_amount,
                'additional_discount'   => $request->get('additional_discount'),
                'additional_cost'   => $request->get('additional_cost'),
                'net_amount'    => $net_total_amount
            ];

            $supplier = Supplier::where('id', $request->get('supplier_id'))->first();
            $supplier_ledger = SupplierLedger::where('supplier_id', $request->get('supplier_id'))->orderBy('id', 'DESC')->first();

            if(empty($supplier_ledger)) {
                $supplier_closing_balance = 0;
            }else{
                $supplier_closing_balance = $supplier_ledger->closing_balance;
            }

            $closing_balance = ($supplier_closing_balance + ($net_total_amount - $total_free_amount));
            $supplier_ledger_inputs = [
                'supplier_id'   => $request->get('supplier_id'),
                'transaction_type'  => 'PR',
                'opening_balance'   => $supplier_closing_balance,
                'purchase_receive_amount'   => $net_total_amount - $total_free_amount,
                'closing_balance'   => $closing_balance,
                'transaction_date'  => date("Y-m-d"),
            ];

            // For Central Ledger
            $specific_ledger = ($supplier->payable_accounts) ? $supplier : "";
            $company_id = auth()->user()->company_id ?? 1;
            $ledger_data    = getLedgerAccounts($company_id,'grn', $specific_ledger);
            //  return response()->json($ledger_data);

            $transactions = [];
            if(count($ledger_data) > 0) {
                $t = 0;
                foreach ($ledger_data as $key => $v) {
                    $account_ledger = AccountLedger::where('ledger_code', $key)->first();
                    if($v == 'D') {
                        $debit_amount = $net_total_amount - $total_free_amount;
                        $credit_amount = 0;
                        $vaccount_type = 'dr';
                    }else{
                        $credit_amount = $net_total_amount - $total_free_amount;
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
                        'reference_id'  => $reference_id,
                        'transaction_sl'    => 1,
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
                'vtype_value'   => 'auto voucher',
                'fiscal_year_id' => $fiscal_year->id,
                'vdate' => $request->get('purchase_date'),
                'global_note'   => 'Purchase product receive from supplier',
                'modified_item' => 0,
            ];

            // Final Submission
            if(count($product_ids) > 0) {

                DB::beginTransaction();
                try{

                    for ($i=0; $i<count($product_ids); $i++) {

                        $purchaseProducts = Product::find($product_ids[$i]);
                        if(!empty($purchaseProducts)) {

                            // Expirable Product
                            if($purchaseProducts->is_expirable && count($product_expires_data[$purchaseProducts->id]) > 0) {

                                for($pe=0; $pe<count($product_expires_data[$purchaseProducts->id]); $pe++) {
                                    $stock_product = WarehouseStockProduct::where('product_id', $product_expires_data[$purchaseProducts->id][$pe]['product_id'])->where('warehouse_id', $warehouse_id)->where('expires_date', $product_expires_data[$purchaseProducts->id][$pe]['expire_date'])->first();
                                    $stock_qty = $stock_product->stock_quantity ?? 0;
                                    // Stock Not Exists
                                    if(empty($stock_product)) {
                                        $stock_new_inputs = [
                                            'product_id'    => $product_expires_data[$purchaseProducts->id][$pe]['product_id'],
                                            'warehouse_id'         => $warehouse_id,
                                            'in_stock_quantity' => $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                            'stock_quantity' => $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                            'expires_date' => $product_expires_data[$purchaseProducts->id][$pe]['expire_date'],
                                        ];

                                        $stock_new_products = WarehouseStockProduct::create($stock_new_inputs);

                                        // For average tp && Sale Price Update
                                        $pro_update_inputs = [
                                            'cost_price' => $product_array[$purchaseProducts->id]['receive_cost_price'],
                                            'mrp_price' => $product_array[$purchaseProducts->id]['receive_mrp_price']
                                        ];
                                        $product_update = $purchaseProducts->update($pro_update_inputs);
                                    }
                                    // Stock is Exists
                                    else{
                                        $update_stock_input = [
                                            'in_stock_quantity' => $stock_product->in_stock_quantity + $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                            'stock_quantity'    => $stock_product->stock_quantity + $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],

                                        ];

                                        $stock_product->update($update_stock_input);

                                        // For Average TP
                                        $stock_product_value = $stock_qty * $purchaseProducts->cost_price;
                                        $receive_product_qty = $product_array[$purchaseProducts->id]['receive_quantity'];
                                        $receive_product_tp = $product_array[$purchaseProducts->id]['receive_cost_price'];
                                        $receive_product_value  = $receive_product_tp * $receive_product_qty;

                                        $average_tp = ($stock_product_value + $receive_product_value) / ($stock_qty + $receive_product_qty);

                                        $pro_update_inputs = [
                                            'cost_price' => $average_tp,
                                            'mrp_price' => $product_array[$purchaseProducts->id]['receive_mrp_price']
                                        ];
                                        $product_update = $purchaseProducts->update($pro_update_inputs);
                                    }

                                    $stock_log_inputs[]    = [
                                        'product_id' => $product_expires_data[$purchaseProducts->id][$pe]['product_id'],
                                        'category_id'    => $purchaseProducts->sub_category_id,
                                        'warehouse_id' => $warehouse_id,
                                        'in_stock_quantity' => $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                        'stock_quantity'    => ($stock_qty + $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity']),
                                        'expires_date'  => $product_expires_data[$purchaseProducts->id][$pe]['expire_date'],
                                        'stock_type'    => 'PR',
                                        'user_id'   => auth()->user()->id ?? 1,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }

                            }
                            // Not Expirable Product
                            else{
                                $stock_product = WarehouseStockProduct::where('product_id', $purchaseProducts->id)->where('warehouse_id', $warehouse_id)->first();

                                $stock_qty = $stock_product->stock_quantity ?? 0;
                                $stock_weight = $stock_product->stock_weight ?? 0;
                                // Stock Not Exists
                                if(empty($stock_product)) {
                                    $new_stock_input = [
                                        'product_id'    => $purchaseProducts->id,
                                        'category_id'    => $purchaseProducts->sub_category_id,
                                        'warehouse_id'         => $warehouse_id,
                                        'in_stock_quantity' => $product_array[$purchaseProducts->id]['receive_quantity'],
                                        'in_stock_weight' => $product_array[$purchaseProducts->id]['receive_weight'],
                                        'stock_quantity' => $product_array[$purchaseProducts->id]['receive_quantity'],
                                        'stock_weight' => $product_array[$purchaseProducts->id]['receive_weight'],
                                    ];

                                    $create_new_stock = WarehouseStockProduct::create($new_stock_input);

                                    // For average tp && Sale Price Update
                                    $pro_update_inputs = [
                                        'cost_price' => $product_array[$purchaseProducts->id]['receive_cost_price'],
                                        'mrp_price' => $product_array[$purchaseProducts->id]['receive_mrp_price']
                                    ];
                                    $product_update = $purchaseProducts->update($pro_update_inputs);

                                }
                                // Stock is exists
                                else{
                                    $update_stock = [
                                        'in_stock_quantity' => $stock_product->in_stock_quantity + $product_array[$purchaseProducts->id]['receive_quantity'],
                                        'in_stock_weight' => $stock_product->in_stock_weight + $product_array[$purchaseProducts->id]['receive_weight'],
                                        'stock_quantity' => $stock_product->stock_quantity + $product_array[$purchaseProducts->id]['receive_quantity'],
                                        'stock_weight' => $stock_product->stock_weight + $product_array[$purchaseProducts->id]['receive_weight'],
                                    ];
                                    $stock_product->update($update_stock);

                                    // For Average TP
                                    $stock_product_value = $stock_qty * $purchaseProducts->cost_price;
                                    $receive_product_qty = $product_array[$purchaseProducts->id]['receive_quantity'];
                                    $receive_product_tp = $product_array[$purchaseProducts->id]['receive_cost_price'];
                                    $receive_product_value  = $receive_product_tp * $receive_product_qty;

                                    $average_tp = ($stock_product_value + $receive_product_value) / ($stock_qty + $receive_product_qty);

                                    // For average tp && Sale Price Update
                                    $pro_update_inputs = [
                                        'cost_price' => $average_tp,
                                        'mrp_price' => $product_array[$purchaseProducts->id]['receive_mrp_price']
                                    ];
                                    $product_update = $purchaseProducts->update($pro_update_inputs);

                                }

                                $stock_log_inputs[]    = [
                                    'product_id' => $purchaseProducts->id,
                                    'category_id'    => $purchaseProducts->sub_category_id,
                                    'warehouse_id' => $warehouse_id,
                                    'in_stock_quantity' => $product_array[$purchaseProducts->id]['receive_quantity'],
                                    'in_stock_weight' => $product_array[$purchaseProducts->id]['receive_weight'],
                                    'stock_quantity'    => ($stock_qty + $product_array[$purchaseProducts->id]['receive_quantity']),
                                    'stock_weight'    => ($stock_weight + $product_array[$purchaseProducts->id]['receive_weight']),
                                    'expires_date'  => NULL,
                                    'stock_type'    => 'PR',
                                    'user_id'   => auth()->user()->id ?? 1,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ];
                            }

                            // Add Product Gift and Gift Stock
                            if(count($product_gifts_data[$purchaseProducts->id]) > 0) {

                                for($pg=0; $pg<count($product_gifts_data[$purchaseProducts->id]); $pg++) {

                                    $stock_product_gift = WarehouseStockProductGift::where('product_id', $product_gifts_data[$purchaseProducts->id][$pg]['product_id'])->where('outlet_id', $request->get('outlet_id'))->where('gift_name', $product_gifts_data[$purchaseProducts->id][$pg]['gift_name'])->first();
                                    if(empty($stock_product_gift)) {
                                        $new_stock_gift_inputs = [
                                            'product_id'    => $product_gifts_data[$purchaseProducts->id][$pg]['product_id'],
                                            'warehouse_id'         => $warehouse_id,
                                            'gift_name' => $product_gifts_data[$purchaseProducts->id][$pg]['gift_name'],
                                            'stock_quantity'    => $product_gifts_data[$purchaseProducts->id][$pg]['gift_quantity'],
                                        ];

                                        $new_stock_gift = WarehouseStockProductGift::create($new_stock_gift_inputs);

                                    }else{
                                        $update_stock_gift_inputs = [
                                            'stock_quantity'    => $stock_product_gift->stock_quantity + $product_gifts_data[$purchaseProducts->id][$pg]['gift_quantity'],
                                        ];

                                        $update_stock_gift = $stock_product_gift->update($update_stock_gift_inputs);
                                    }
                                }

                            }


                        }

                    }

                    $purchase_receive_save = $this->purchaseReceiveRepository->create($receive_inputs);

                    // For Purchase Receive Details
                    if(count($receive_details_product_array) > 0) {

                        for($pr=0; $pr<count($receive_details_product_array); $pr++) {
                            $ex_product_id  = $receive_details_product_array[$pr]['receive_product_id'];
                            $receive_details_save = $purchase_receive_save->purchase_receive_details()->create($receive_details_product_array[$pr]);

                            // Product Expires Data
                            if(array_key_exists($ex_product_id, $product_expires_data) && count($product_expires_data[$ex_product_id]) > 0) {
                                for($pex=0; $pex<count($product_expires_data[$ex_product_id]); $pex++) {
                                    $pex_insert_inputs    = [
                                        'direct_pr_details_id'    => $receive_details_save->id,
                                        'product_id'    => $product_expires_data[$ex_product_id][$pex]['product_id'],
                                        'expire_date'   => $product_expires_data[$ex_product_id][$pex]['expire_date'],
                                        'expire_quantity'      => $product_expires_data[$ex_product_id][$pex]['expire_quantity'],
                                        'created_at'    => Carbon::now()->toDateTimeString(),
                                        'updated_at'    => Carbon::now()->toDateTimeString(),
                                    ];

                                    $product_expires_date_save = ProductExpiresDate::create($pex_insert_inputs);
                                }
                            }

                            // Products Gift Data
                            if(array_key_exists($ex_product_id, $product_gifts_data) && count($product_gifts_data[$ex_product_id]) > 0) {
                                for($pg=0; $pg<count($product_gifts_data[$ex_product_id]); $pg++) {
                                    $pg_insert_inputs    = [
                                        'direct_pr_details_id'    => $receive_details_save->id,
                                        'product_id'    => $product_gifts_data[$ex_product_id][$pg]['product_id'],
                                        'gift_name'   => $product_gifts_data[$ex_product_id][$pg]['gift_name'],
                                        'gift_quantity' => $product_gifts_data[$ex_product_id][$pg]['gift_quantity'],
                                        'created_at'    => Carbon::now()->toDateTimeString(),
                                        'updated_at'    => Carbon::now()->toDateTimeString(),
                                    ];

                                    $product_gifts_save = ProductGift::create($pg_insert_inputs);
                                }
                            }

                        }

                    }

                    // For Stock Logs
                    if(count($stock_log_inputs) > 0) {
                        for($slg=0; $slg<count($stock_log_inputs); $slg++) {
                            $stock_log_inputs[$slg]['purchase_receive_id'] = $purchase_receive_save->id;
                        }
                        $stock_log_insert = WarehouseStockProductLog::insert($stock_log_inputs);
                    }

                    $voucher_save = AccountVoucher::create($voucher_inputs);
                    $transaction_save = $voucher_save->account_voucher_transactions()->saveMany($transactions);
                    $supplier_ledger_inputs['purchase_receive_id']  = $purchase_receive_save->id;
                    $supplier_ledger_inputs['voucher_id']  = $voucher_save->id;
                    $supplier_ledger_save = SupplierLedger::create($supplier_ledger_inputs);
                    DB::commit();
                    return $this->sendResponse($purchase_receive_save->toArray(), 'Purchase Receive saved successfully');
                }catch(\Exception $exception){
                    DB::rollBack();
                    return $this->sendError($exception->getMessage());
                }
            }else{
                return $this->sendError('Please select/check at least one products item');
            }

        }
        // For Requisition Order Based Purchase Receive
        else{
            $purchase_order = PurchaseOrder::find($request->get('purchase_order_id'));
//            $store_requisition = StoreRequisition::find($purchase_order->store_requisition_id);
            $warehouse_id = $request->get('warehouse_id');

            $products = json_decode($request->get('products'));

            $order_details_product = [];
            $product_array = [];
            $product_receive_details = [];
            $rcv_product_details_ids = [];
            $stock_log_inputs = [];
            $product_details_ids = [];
            $product_ids = [];
            $product_expires_data = [];
            $product_gifts_data = [];
            $item_rcv_quantity = 0;
            $total_rcv_quantity = 0;
            $total_rcv_product_value = 0;
            $total_free_amount = 0;
            $total_discount_amount = 0;
            $total_rcv_amount = 0;
            $total_vat_amount = 0;
            if(count($products) > 0)
            {
                foreach ($products as $product) {

                    if($product->id != "" && ($product->purchase_price != 0 && $product->purchase_price != "") && ($product->sale_price != 0 && $product->sale_price != "") && ($product->rcv_qty != 0 && $product->rcv_qty != "")) {

                        $product_amount = $product->rcv_qty * $product->purchase_price;
                        $free_amount = $product->free_qty * $product->purchase_price;
                        $product_rcv_amount = ($product_amount - $product->disc_amount) + 0;
                        $item_rcv_quantity = ($product->prcv_qty + $product->rcv_qty);

                        if($item_rcv_quantity >= $product->ord_qty) {
                            $prcv_status    = 2; // Full RCV
                            $rcv_product_details_ids[] = $product->order_details_id;
                        }else{
                            $prcv_status    = 1; //Partial RCV
                        }

                        $product_ids[]  = $product->id;
                        $product_details_ids[]  = $product->order_details_id;
                        $product_array[$product->id]    = [
                            'receive_quantity'          => $product->rcv_qty,
                            'receive_weight'            => $product->rcv_weight,
                            'receive_cost_price'        => $product->purchase_price,
                            'receive_mrp_price'         => $product->sale_price,
                        ];

                        $order_details_product[$product->order_details_id]    = [
                            'receive_quantity'          => $item_rcv_quantity,
                            'receive_status'            => $prcv_status,
                        ];

                        $product_receive_details[] = [
                            'order_details_id'          => $product->order_details_id,
                            'receive_product_id'        => $product->id,
                            'receive_product_unit_id'   => $product->product_unit_id,
                            'receive_supplier_id'       => $request->get('supplier_id'),
                            'receive_purchase_price'    => $product->purchase_price,
                            'receive_mrp_price'         => $product->sale_price,
                            'receive_order_quantity'    => $product->ord_qty,
                            'receive_quantity'          => $product->rcv_qty,
                            'receive_discount_percent'  => $product->disc_percent,
                            'receive_discount_amount'   => $product->disc_amount,
                            'receive_free_quantity'     => $product->free_qty,
                            'receive_free_amount'       => $free_amount,
                            'receive_product_value'     => $product_amount,
                            'receive_vat_amount'        => 0,
                            'receive_amount'            => $product_rcv_amount,
                        ];


                        $total_rcv_quantity += $product->rcv_qty;
                        $total_rcv_product_value    += $product_amount;
                        $total_free_amount  += $free_amount;
                        $total_discount_amount  += $product->disc_amount;
                        $total_vat_amount   += 0;
                        $total_rcv_amount   += $product_rcv_amount;

                        $product_expires_data[$product->id] = [];
                        if($product->is_expirable && $product->is_expires && count($product->expires_data) > 0) {
                            foreach ($product->expires_data as $expires_data) {
                                if($expires_data->expire_date != "" && ($expires_data->expire_qty != 0 && $expires_data->expire_qty != "")) {
                                    $product_expires_data[$product->id][] = [
                                        'product_id' => $product->id,
                                        'expire_date' => $expires_data->expire_date,
                                        'expire_quantity' => $expires_data->expire_qty,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }
                            }
                        }

                        $product_gifts_data[$product->id] = [];
                        if($product->is_gifts && count($product->gifts) > 0) {
                            foreach ($product->gifts as $gift) {
                                if($gift->gift_name != "" && ($gift->gift_qty != 0 && $gift->gift_qty != "")) {
                                    $product_gifts_data[$product->id][] = [
                                        'product_id' => $product->id,
                                        'gift_name' => $gift->gift_name,
                                        'gift_quantity' => $gift->gift_qty,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            $net_total_amount = ($total_rcv_amount - $request->get('additional_discount')) + $request->get('additional_cost');

            $receive_inputs = [
                'purchase_order_id' => $request->get('purchase_order_id'),
                'supplier_id'       => $request->get('supplier_id'),
                'receive_type'      => 'SR',
                'reference_no'      => $request->get('reference_no'),
                'challan_no'      => $request->get('challan_no'),
                'purchase_date'     => $request->get('purchase_date'),
                'warehouse_id'         => $warehouse_id,
                'delivery_to'       => 0,
                'total_rcv_quantity' => $total_rcv_quantity,
                'total_rcv_value'   => $total_rcv_product_value,
                'total_commission_value' => $total_discount_amount,
                'total_vat'     => $total_vat_amount,
                'total_free_amount' => $total_free_amount,
                'total_amount'  => $total_rcv_amount,
                'additional_discount'   => $request->get('additional_discount'),
                'additional_cost'   => $request->get('additional_cost'),
                'net_amount'    => $net_total_amount
            ];

            // For Supplier Ledger
            $supplier = Supplier::where('id', $request->get('supplier_id'))->first();
            $supplier_ledger = SupplierLedger::where('supplier_id', $request->get('supplier_id'))->orderBy('id', 'DESC')->first();

            if(empty($supplier_ledger)) {
                $supplier_closing_balance = 0;
            }else{
                $supplier_closing_balance = $supplier_ledger->closing_balance;
            }

            $closing_balance = ($supplier_closing_balance + ($net_total_amount - $total_free_amount));
            $supplier_ledger_inputs = [
                'supplier_id'   => $request->get('supplier_id'),
                'transaction_type'  => 'PR',
                'opening_balance'   => $supplier_closing_balance,
                'purchase_receive_amount'   => $net_total_amount - $total_free_amount,
                'closing_balance'   => $closing_balance,
                'transaction_date'  => date("Y-m-d"),
            ];

            // For Central Ledger
            $specific_ledger = ($supplier->payable_accounts) ? $supplier : "";
            $company_id = auth()->user()->company_id ?? 1;
            $ledger_data    = getLedgerAccounts($company_id,'grn', $specific_ledger);
            $transactions = [];
            if(count($ledger_data) > 0) {
                $i = 0;
                foreach ($ledger_data as $key => $v) {
                    $account_ledger = AccountLedger::where('ledger_code', $key)->first();
                    if($v == 'D') {
                        $debit_amount = $net_total_amount - $total_free_amount;
                        $credit_amount = 0;
                        $vaccount_type = 'dr';
                    }else{
                        $credit_amount = $net_total_amount - $total_free_amount;
                        $debit_amount = 0;
                        $vaccount_type = 'cr';
                    }

//                    if($i == 0) {
//                        $reference_id = NULL;
//                    }else{
//                        $reference_ledger = AccountLedger::where('ledger_code', '120101')->first();
//                        $reference_id = $reference_ledger->id;
//                    }
                    if($i == 0) {
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
                        'reference_id'  => $reference_id,
                        'transaction_sl'    => 1,
                        'created_at'    => date("Y-m-d H:i:s"),
                        'updated_at'    => date("Y-m-d H:i:s"),
                    ]);

                    $i++;
                }
            }

            $entry_type = EntryType::where('label', 'journal')->first();
            $voucher_inputs = [
                'vcode' => $this->returnVoucherCode('journal'),
                'vtype_id'  => $entry_type->id,
                'vtype_value'   => 'auto_voucher',
                'fiscal_year_id' => $fiscal_year->id,
                'vdate' => $request->get('purchase_date'),
            ];

            if(count($product_details_ids) > 0 && count($product_ids) > 0) {

                DB::beginTransaction();
                try{

                    for ($p=0; $p<count($product_ids); $p++) {

                        $purchaseProduct = Product::find($product_ids[$p]);
                        if(!empty($purchaseProduct)) {

                            // Expirable Product
                            if($purchaseProduct->is_expirable && count($product_expires_data[$purchaseProduct->id]) > 0) {

                                for($pe=0; $pe<count($product_expires_data[$purchaseProduct->id]); $pe++) {
                                    $stock_product = WarehouseStockProduct::where('product_id', $product_expires_data[$purchaseProduct->id][$pe]['product_id'])->where('warehouse_id', $warehouse_id)->where('expires_date', $product_expires_data[$purchaseProduct->id][$pe]['expire_date'])->first();
                                    $stock_qty = $stock_product->stock_quantity ?? 0;

                                    // Stock Not Exists
                                    if(empty($stock_product)) {
                                        $stock_new_inputs = [
                                            'product_id'    => $product_expires_data[$purchaseProduct->id][$pe]['product_id'],
                                            'category_id'    => $purchaseProduct->sub_category_id,
                                            'warehouse_id'         => $warehouse_id,
                                            'in_stock_quantity' => $product_expires_data[$purchaseProduct->id][$pe]['expire_quantity'],
                                            'stock_quantity' => $product_expires_data[$purchaseProduct->id][$pe]['expire_quantity'],
                                            'expires_date' => $product_expires_data[$purchaseProduct->id][$pe]['expire_date'],
                                        ];

                                        $stock_new_products = WarehouseStockProduct::create($stock_new_inputs);
                                        // For average tp && Sale Price Update
                                        $pro_update_inputs = [
                                            'cost_price' => $product_array[$purchaseProduct->id]['receive_cost_price'],
                                            'mrp_price' => $product_array[$purchaseProduct->id]['receive_mrp_price']
                                        ];
                                        $product_update = $purchaseProduct->update($pro_update_inputs);

                                    }
                                    // Stock Exists
                                    else{
                                        $update_stock_input = [
                                            'in_stock_quantity' => $stock_product->in_stock_quantity + $product_expires_data[$purchaseProduct->id][$pe]['expire_quantity'],
                                            'stock_quantity'    => $stock_product->stock_quantity + $product_expires_data[$purchaseProduct->id][$pe]['expire_quantity'],

                                        ];

                                        $stock_product->update($update_stock_input);

                                        // For Average TP
                                        $stock_product_value = $stock_qty * $purchaseProduct->cost_price;
                                        $receive_product_qty = $product_array[$purchaseProduct->id]['receive_quantity'];
                                        $receive_product_tp = $product_array[$purchaseProduct->id]['receive_cost_price'];
                                        $receive_product_value  = $receive_product_tp * $receive_product_qty;

                                        $average_tp = ($stock_product_value + $receive_product_value) / ($stock_qty + $receive_product_qty);

                                        $pro_update_inputs = [
                                            'cost_price' => $average_tp,
                                            'mrp_price' => $product_array[$purchaseProduct->id]['receive_mrp_price']
                                        ];
                                        $product_update = $purchaseProduct->update($pro_update_inputs);
                                    }

                                    $stock_log_inputs[]    = [
                                        'product_id' => $product_expires_data[$purchaseProduct->id][$pe]['product_id'],
                                        'category_id'    => $purchaseProduct->sub_category_id,
                                        'warehouse_id' => $warehouse_id,
                                        'in_stock_quantity' => $product_expires_data[$purchaseProduct->id][$pe]['expire_quantity'],
                                        'stock_quantity'    => ($stock_qty + $product_expires_data[$purchaseProduct->id][$pe]['expire_quantity']),
                                        'expires_date'  => $product_expires_data[$purchaseProduct->id][$pe]['expire_date'],
                                        'stock_type'    => 'PR',
                                        'user_id'   => auth()->user()->id ?? 1,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }

                            }
                            // Not Expirable Product
                            else{
                                $stock_product = WarehouseStockProduct::where('product_id', $purchaseProduct->id)->where('warehouse_id', $warehouse_id)->first();

                                $stock_qty = $stock_product->stock_quantity ?? 0;
                                $stock_weight = $stock_product->stock_weight ?? 0;
                                // Stock Not Exists
                                if(empty($stock_product)) {
                                    $new_stock_input = [
                                        'product_id'    => $purchaseProduct->id,
                                        'category_id'    => $purchaseProduct->sub_category_id,
                                        'warehouse_id'         => $warehouse_id,
                                        'in_stock_quantity' => $product_array[$purchaseProduct->id]['receive_quantity'],
                                        'in_stock_weight' => $product_array[$purchaseProduct->id]['receive_weight'],
                                        'stock_quantity' => $product_array[$purchaseProduct->id]['receive_quantity'],
                                        'stock_weight' => $product_array[$purchaseProduct->id]['receive_weight'],
                                    ];

                                    $create_new_stock = WarehouseStockProduct::create($new_stock_input);
                                    // For average tp && Sale Price Update
                                    $pro_update_inputs = [
                                        'cost_price' => $product_array[$purchaseProduct->id]['receive_cost_price'],
                                        'mrp_price' => $product_array[$purchaseProduct->id]['receive_mrp_price']
                                    ];
                                    $product_update = $purchaseProduct->update($pro_update_inputs);

                                }
                                // Stock is exists
                                else{
                                    $update_stock = [
                                        'in_stock_quantity' => $stock_product->in_stock_quantity + $product_array[$purchaseProduct->id]['receive_quantity'],
                                        'in_stock_weight' => $stock_product->in_stock_weight + $product_array[$purchaseProduct->id]['receive_weight'],
                                        'stock_quantity' => $stock_product->stock_quantity + $product_array[$purchaseProduct->id]['receive_quantity'],
                                        'stock_weight' => $stock_product->stock_weight + $product_array[$purchaseProduct->id]['receive_weight'],
                                    ];
                                    $stock_product->update($update_stock);

                                    // For Average TP
                                    $stock_product_value = $stock_qty * $purchaseProduct->cost_price;
                                    $receive_product_qty = $product_array[$purchaseProduct->id]['receive_quantity'];
                                    $receive_product_tp = $product_array[$purchaseProduct->id]['receive_cost_price'];
                                    $receive_product_value  = $receive_product_tp * $receive_product_qty;

                                    $average_tp = ($stock_product_value + $receive_product_value) / ($stock_qty + $receive_product_qty);

                                    // For average tp && Sale Price Update
                                    $pro_update_inputs = [
                                        'cost_price' => $average_tp,
                                        'mrp_price' => $product_array[$purchaseProduct->id]['receive_mrp_price']
                                    ];
                                    $product_update = $purchaseProduct->update($pro_update_inputs);

                                }

                                $stock_log_inputs[]    = [
                                    'product_id' => $purchaseProduct->id,
                                    'category_id'    => $purchaseProduct->sub_category_id,
                                    'warehouse_id' => $warehouse_id,
                                    'in_stock_quantity' => $product_array[$purchaseProduct->id]['receive_quantity'],
                                    'in_stock_weight' => $product_array[$purchaseProduct->id]['receive_weight'],
                                    'stock_quantity'    => ($stock_qty + $product_array[$purchaseProduct->id]['receive_quantity']),
                                    'stock_weight'    => ($stock_weight + $product_array[$purchaseProduct->id]['receive_weight']),
                                    'expires_date'  => NULL,
                                    'stock_type'    => 'PR',
                                    'user_id'   => auth()->user()->id ?? 1,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ];
                            }


                            // Add Product Gift and Gift Stock
                            if(count($product_gifts_data[$purchaseProduct->id]) > 0) {

                                for($pg=0; $pg<count($product_gifts_data[$purchaseProduct->id]); $pg++) {

                                    $stock_product_gift = WarehouseStockProductGift::where('product_id', $product_gifts_data[$purchaseProduct->id][$pg]['product_id'])->where('outlet_id', $request->get('outlet_id'))->where('gift_name', $product_gifts_data[$purchaseProduct->id][$pg]['gift_name'])->first();
                                    if(empty($stock_product_gift)) {
                                        $new_stock_gift_inputs = [
                                            'product_id'    => $product_gifts_data[$purchaseProduct->id][$pg]['product_id'],
                                            'warehouse_id'         => $warehouse_id,
                                            'gift_name' => $product_gifts_data[$purchaseProduct->id][$pg]['gift_name'],
                                            'stock_quantity'    => $product_gifts_data[$purchaseProduct->id][$pg]['gift_quantity'],
                                        ];

                                        $new_stock_gift = WarehouseStockProductGift::create($new_stock_gift_inputs);

                                    }else{
                                        $update_stock_gift_inputs = [
                                            'stock_quantity'    => $stock_product_gift->stock_quantity + $product_gifts_data[$purchaseProduct->id][$pg]['gift_quantity'],
                                        ];

                                        $update_stock_gift = $stock_product_gift->update($update_stock_gift_inputs);
                                    }
                                }

                            }
                        }

                    }

                    $purchase_receive_save = $this->purchaseReceiveRepository->create($receive_inputs);
                    // For Purchase Receive Details
                    for($pr=0; $pr<count($product_details_ids); $pr++) {
                        $ex_product_id  = $product_receive_details[$pr]['receive_product_id'];
                        $receive_details_save = $purchase_receive_save->purchase_receive_details()->create($product_receive_details[$pr]);

                        // Product Expires Data
                        if(array_key_exists($ex_product_id, $product_expires_data) && count($product_expires_data[$ex_product_id]) > 0) {
                            for($pex=0; $pex<count($product_expires_data[$ex_product_id]); $pex++) {
                                $pex_insert_inputs    = [
                                    'purchase_receive_details_id'    => $receive_details_save->id,
                                    'product_id'    => $product_expires_data[$ex_product_id][$pex]['product_id'],
                                    'expire_date'   => $product_expires_data[$ex_product_id][$pex]['expire_date'],
                                    'expire_quantity'      => $product_expires_data[$ex_product_id][$pex]['expire_quantity'],
                                    'created_at'    => Carbon::now()->toDateTimeString(),
                                    'updated_at'    => Carbon::now()->toDateTimeString(),
                                ];

                                $product_expires_date_save = ProductExpiresDate::create($pex_insert_inputs);
                            }
                        }

                        // Products Gift Data
                        if(array_key_exists($ex_product_id, $product_gifts_data) && count($product_gifts_data[$ex_product_id]) > 0) {
                            for($pg=0; $pg<count($product_gifts_data[$ex_product_id]); $pg++) {
                                $pg_insert_inputs    = [
                                    'purchase_receive_details_id'    => $receive_details_save->id,
                                    'product_id'    => $product_gifts_data[$ex_product_id][$pg]['product_id'],
                                    'gift_name'   => $product_gifts_data[$ex_product_id][$pg]['gift_name'],
                                    'gift_quantity' => $product_gifts_data[$ex_product_id][$pg]['gift_quantity'],
                                    'created_at'    => Carbon::now()->toDateTimeString(),
                                    'updated_at'    => Carbon::now()->toDateTimeString(),
                                ];

                                $product_gifts_save = ProductGift::create($pg_insert_inputs);
                            }
                        }

                        // Order Details Update
                        $purchaseReceiveDetails = OrderRequisitionDetail::find($product_details_ids[$pr]);
                        if(!empty($purchaseReceiveDetails)) {

                            $purchaseReceiveDetails->update($order_details_product[$purchaseReceiveDetails->id]);

                        }

                    }

                    // For Stock Logs
                    if(count($stock_log_inputs) > 0) {
                        for($slg=0; $slg<count($stock_log_inputs); $slg++) {
                            $stock_log_inputs[$slg]['purchase_receive_id'] = $purchase_receive_save->id;
                        }
                        $stock_log_insert = WarehouseStockProductLog::insert($stock_log_inputs);
                    }

                    $voucher_save   = AccountVoucher::create($voucher_inputs);
                    $transaction_save   = $voucher_save->account_voucher_transactions()->saveMany($transactions);
                    $supplier_ledger_inputs['purchase_receive_id']  = $purchase_receive_save->id;
                    $supplier_ledger_inputs['voucher_id']   = $voucher_save->id;
                    $supplier_ledger_save = SupplierLedger::create($supplier_ledger_inputs);

                    if(count($products) == count($rcv_product_details_ids)) {
                        $receive_status = 1;
                    }else{
                        $receive_status = 2;
                    }
                    $purchase_order_update = PurchaseOrder::find($request->get('purchase_order_id'))->update(['receive_status' => $receive_status]);
                    DB::commit();
                    return $this->sendResponse($purchase_receive_save->toArray(), 'Purchase Receive saved successfully');
                }catch(\Exception $exception){
                    DB::rollBack();
                    return $this->sendError($exception->getMessage());
                }
            }else{
                return $this->sendError('Please select/check at least one products item');
            }
        }

    }

    public function storeOld(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required',
            'purchase_order_id' => 'required',
            'purchase_date' => 'required',
            'reference_no'  => 'required',
            'challan_no'    => 'required',
        ]);

        $outlet_id = $request->get('outlet_id');

        $products = json_decode($request->get('products'));

        $product_array = [];
        $product_details_ids = [];
        $product_expires_data = [];
        $product_gifts_data = [];
        $total_rcv_quantity = 0;
        $total_rcv_product_value = 0;
        $total_free_amount = 0;
        $total_discount_amount = 0;
        $total_rcv_amount = 0;
        $total_vat_amount = 0;
        if(count($products) > 0)
        {
            foreach ($products as $product) {

                if($product->checked) {

                    $product_amount = $product->rcv_qty * $product->purchase_price;
                    $free_amount = $product->free_qty * $product->purchase_price;

                    $product_rcv_amount = ($product_amount - $product->disc_amount) + $product->vat;

                    $product_details_ids[]  = $product->order_details_id;
                    $product_array[$product->order_details_id]    = [
                        'receive_purchase_price'    => $product->purchase_price,
                        'receive_quantity'          => $product->rcv_qty,
                        'receive_discount_percent'  => $product->disc_percent,
                        'receive_discount_amount'   => $product->disc_amount,
                        'receive_free_quantity'     => $product->free_qty,
                        'receive_free_amount'       => $free_amount,
                        'receive_product_value'     => $product_amount,
                        'receive_vat_amount'        => $product->vat,
                        'receive_amount'            => $product_rcv_amount,
                        'receive_status'            => 2,
                    ];


                    $total_rcv_quantity += $product->rcv_qty;
                    $total_rcv_product_value    += $product_amount;
                    $total_free_amount  += $free_amount;
                    $total_discount_amount  += $product->disc_amount;
                    $total_vat_amount   += $product->vat;
                    $total_rcv_amount   += $product_rcv_amount;

                    $product_expires_data[$product->order_details_id] = [];
                    if($product->is_expirable && $product->is_expires && count($product->expires_data) > 0) {
                        foreach ($product->expires_data as $expires_data) {
                            $product_expires_data[$product->order_details_id][] = [
                                'purchase_receive_details_id'    => $product->order_details_id,
                                'product_id'    => $product->id,
                                'expire_date'   => $expires_data->expire_date,
                                'expire_quantity'      => $expires_data->expire_qty,
                                'created_at'    => Carbon::now()->toDateTimeString(),
                                'updated_at'    => Carbon::now()->toDateTimeString(),
                            ];
                        }
                    }

                    $product_gifts_data[$product->order_details_id] = [];
                    if($product->is_gifts && count($product->gifts) > 0) {
                        foreach ($product->gifts as $gift) {
                            $product_gifts_data[$product->order_details_id][] = [
                                'purchase_receive_details_id'    => $product->order_details_id,
                                'product_id'    => $product->id,
                                'gift_name'     => $gift->gift_name,
                                'gift_quantity'      => $gift->gift_qty,
                                'created_at'    => Carbon::now()->toDateTimeString(),
                                'updated_at'    => Carbon::now()->toDateTimeString(),
                            ];
                        }
                    }
                }
            }
        }

        $net_total_amount = ($total_rcv_amount - $request->get('additional_discount')) + $request->get('additional_cost');

        $receive_inputs = [
            'purchase_order_id' => $request->get('purchase_order_id'),
            'supplier_id'       => $request->get('supplier_id'),
            'receive_type'      => 'WR',
            'reference_no'      => $request->get('reference_no'),
            'challan_no'      => $request->get('challan_no'),
            'purchase_date'     => $request->get('purchase_date'),
            'warehouse_id'      => 1,
            'delivery_to'       => $outlet_id ?? 0,
            'total_rcv_quantity' => $total_rcv_quantity,
            'total_rcv_value'   => $total_rcv_product_value,
            'total_commission_value' => $total_discount_amount,
            'total_vat'     => $total_vat_amount,
            'total_free_amount' => $total_free_amount,
            'total_amount'  => $total_rcv_amount,
            'additional_discount'   => $request->get('additional_discount'),
            'additional_cost'   => $request->get('additional_cost'),
            'net_amount'    => $net_total_amount
        ];


        $supplier_ledger = SupplierLedger::where('supplier_id', $request->get('supplier_id'))->orderBy('id', 'DESC')->first();

        if(empty($supplier_ledger)) {
            $supplier_closing_balance = 0;
        }else{
            $supplier_closing_balance = $supplier_ledger->closing_balance;
        }

        $closing_balance = ($supplier_closing_balance + ($net_total_amount - $total_free_amount));
        $supplier_ledger_inputs = [
            'supplier_id'   => $request->get('supplier_id'),
            'transaction_type'  => 'PR',
            'opening_balance'   => $supplier_closing_balance,
            'purchase_receive_amount'   => $net_total_amount - $total_free_amount,
            'closing_balance'   => $closing_balance,
            'transaction_date'  => date("Y-m-d"),
        ];

//        return response()->json([
//            'detail_ids'    => $product_details_ids,
//            'product_array' => $product_array,
//            'expires_product'   => $product_expires_data,
//            'gifts_item'    => $product_gifts_data,
//            'receive_inputs'    => $receive_inputs,
//            'supplier_ledger' => $supplier_ledger_inputs
//        ]);

        if(count($product_details_ids) > 0) {

            DB::beginTransaction();
            try{

                for ($i=0; $i<count($product_details_ids); $i++) {

                    $purchaseReceiveDetails = OrderRequisitionDetail::find($product_details_ids[$i]);
                    if(!empty($purchaseReceiveDetails)) {

                        $purchaseReceiveDetails->update($product_array[$purchaseReceiveDetails->id]);

                        // Expirable Product
                        if($purchaseReceiveDetails->product->is_expirable && count($product_expires_data[$purchaseReceiveDetails->id]) > 0) {

                            for($pe=0; $pe<count($product_expires_data[$purchaseReceiveDetails->id]); $pe++) {
                                $stock_product = WarehouseStockProduct::where('product_id', $product_expires_data[$purchaseReceiveDetails->id][$pe]['product_id'])->where('warehouse_id', 1)->where('expires_date', $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_date'])->first();
                                if(empty($stock_product)) {
                                    $stock_new_inputs = [
                                        'product_id'    => $product_expires_data[$purchaseReceiveDetails->id][$pe]['product_id'],
                                        'warehouse_id'         => 1,
                                        'in_stock_quantity' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                        'stock_quantity' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                        'expires_date' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_date'],
                                    ];

                                    $stock_new_products = WarehouseStockProduct::create($stock_new_inputs);

                                }else{
                                    $update_stock_input = [
                                        'in_stock_quantity' => $stock_product->in_stock_quantity + $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                        'stock_quantity'    => $stock_product->stock_quantity + $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],

                                    ];

                                    $stock_product->update($update_stock_input);
                                }
                            }

                            $product_expire_bulk_insert = ProductExpiresDate::insert($product_expires_data[$purchaseReceiveDetails->id]);

                        }
                        // Not Expirable Product
                        else{
                            $stock_product = WarehouseStockProduct::where('product_id', $purchaseReceiveDetails->product_id)->where('warehouse_id', 1)->first();
                            // Stock Not Exists
                            if(empty($stock_product)) {
                                $new_stock_input = [
                                    'product_id'    => $purchaseReceiveDetails->product_id,
                                    'warehouse_id'         => 1,
                                    'in_stock_quantity' => $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                    'stock_quantity' => $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                ];

                                $create_new_stock = WarehouseStockProduct::create($new_stock_input);
                            }
                            // Stock is exists
                            else{
                                $update_stock = [
                                    'in_stock_quantity' => $stock_product->in_stock_quantity + $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                    'stock_quantity' => $stock_product->stock_quantity + $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                ];
                                $stock_product->update($update_stock);
                            }
                        }


                        // Add Product Gift and Gift Stock
                        if(count($product_gifts_data[$purchaseReceiveDetails->id]) > 0) {

                            for($pg=0; $pg<count($product_gifts_data[$purchaseReceiveDetails->id]); $pg++) {

                                $stock_product_gift = WarehouseStockProductGift::where('product_id', $product_gifts_data[$purchaseReceiveDetails->id][$pg]['product_id'])->where('warehouse_id', 1)->where('gift_name', $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_name'])->first();
                                if(empty($stock_product_gift)) {
                                    $new_stock_gift_inputs = [
                                        'product_id'    => $product_gifts_data[$purchaseReceiveDetails->id][$pg]['product_id'],
                                        'warehouse_id'         => 1,
                                        'gift_name' => $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_name'],
                                        'stock_quantity'    => $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_quantity'],
                                    ];

                                    $new_stock_gift = WarehouseStockProductGift::create($new_stock_gift_inputs);

                                }else{
                                    $update_stock_gift_inputs = [
                                        'stock_quantity'    => $stock_product_gift->stock_quantity + $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_quantity'],
                                    ];

                                    $update_stock_gift = $stock_product_gift->update($update_stock_gift_inputs);
                                }
                            }
                            //Gift Bulk Insert
                            $gift_bulk_insert = ProductGift::insert($product_gifts_data[$purchaseReceiveDetails->id]);
                        }
                    }

                }

                $purchase_receive_save = $this->purchaseReceiveRepository->create($receive_inputs);
                $receive_details_update = OrderRequisitionDetail::whereIn('id', $product_details_ids)->update(['purchase_receive_id' => $purchase_receive_save->id]);
                $supplier_ledger_save = SupplierLedger::create($supplier_ledger_inputs);

                if(count($products) == count($product_details_ids)) {
                    $receive_status = 1;
                }else{
                    $receive_status = 2;
                }
                $purchase_order_update = PurchaseOrder::find($request->get('purchase_order_id'))->update(['receive_status' => $receive_status]);
                DB::commit();
                return $this->sendResponse($purchase_receive_save->toArray(), 'Purchase Receive saved successfully');
            }catch(\Exception $exception){
                DB::rollBack();
                return $this->sendError($exception->getMessage());
            }
        }else{
            return $this->sendError('Please select/check at least one products item');
        }

    }

    /**
     * Display the specified PurchaseReceive.
     * GET|HEAD /warehousePurchaseReceives/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified PurchaseReceive in storage.
     * PUT/PATCH /purchaseReceives/{id}
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {

    }

    /**
     * Remove the specified PurchaseReceive from storage.
     * DELETE /purchaseReceives/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {

    }


    // Get Reference No
    public function getReferenceNo()
    {
        $reference_no = $this->returnPurchaseReceiveReferenceNo('WR', 1);

        return $this->sendResponse(['reference_no' => $reference_no], 'Reference no get successfully');
    }

    // get supplier purchase order by supplier id
    public function supplierPurchaseOrder(Request $request)
    {
        $purchaseOrders = PurchaseOrder::where('supplier_id', $request->supplier_id)
            ->whereNull('store_requisition_id')
            ->where('receive_status', '!=', 1)
            ->where('approve_status', 1)
            ->get();

        $data   = PurchaseOrderResource::collection($purchaseOrders);


        return $this->sendResponse($data, "Purchase Order Retrieve Successfully");

    }


}
