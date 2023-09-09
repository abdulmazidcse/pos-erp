<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReceiveAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReceiveAPIRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Imports\PurchaseReceiveProductImport;
use App\Models\OrderRequisitionDetail;
use App\Models\Product;
use App\Models\ProductExpiresDate;
use App\Models\ProductGift;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveDetail;
use App\Models\StockProduct;
use App\Models\StockProductGift;
use App\Models\StockProductsLog;
use App\Models\StoreRequisition;
use App\Models\SupplierLedger;
use App\Repositories\PurchaseReceiveRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class PurchaseReceiveController
 * @package App\Http\Controllers\API
 */

class PurchaseReceiveAPIController extends AppBaseController
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
//        $purchaseReceives = $this->purchaseReceiveRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );

        $purchaseReceives = PurchaseReceive::orderBy('id', 'desc')->get();

        return $this->sendResponse($purchaseReceives->toArray(), 'Purchase Receives retrieved successfully');
    }

    /**
     * Store a newly created PurchaseReceive in storage.
     * POST /purchaseReceives
     *
     * @param CreatePurchaseReceiveAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseReceiveAPIRequest $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required',
            'purchase_order_id' => 'required',
            'purchase_date' => 'required',
            'reference_no'  => 'required',
            'challan_no'    => 'required'
        ]);

        // For Without purchase order to receive product
        if($request->purchase_order_id == "direct") {
            $outlet_id = auth()->user()->outlet_id ?? 1;

            $products = json_decode($request->get('products'));

            $product_array = [];
            $receive_details_product_array = [];
            $stock_log_inputs = [];
            $product_ids = [];
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

                    if($product->id != "" && ($product->purchase_price != 0 && $product->purchase_price != "") && ($product->sale_price != 0 && $product->sale_price != "") && ($product->rcv_qty != 0 && $product->rcv_qty != "")) {

                        $product_amount = $product->rcv_qty * $product->purchase_price;
                        $free_amount = $product->free_qty * $product->purchase_price;

                        $product_rcv_amount = ($product_amount - $product->disc_amount) + 0;

                        $product_ids[]  = $product->id;
                        $product_array[$product->id]    = [
                            'receive_purchase_price'    => $product->purchase_price,
                            'receive_supplier_id'       => $request->get('supplier_id'),
                            'receive_mrp_price'         => $product->sale_price,
                            'receive_quantity'          => $product->rcv_qty,
                            'receive_discount_percent'  => $product->disc_percent,
                            'receive_discount_amount'   => $product->disc_amount,
                            'receive_free_quantity'     => $product->free_qty,
                            'receive_free_amount'       => $free_amount,
                            'receive_product_value'     => $product_amount,
                            'receive_vat_amount'        => 0,
                            'receive_amount'            => $product_rcv_amount,
                            'receive_status'            => 2,
                        ];

//                        $receive_details_product_array[]   = new PurchaseReceiveDetails([
                        $receive_details_product_array[]   = [
                            'receive_product_id'        => $product->id,
                            'receive_purchase_price'    => $product->purchase_price,
                            'receive_mrp_price'         => $product->sale_price,
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
                'outlet_id'         => $outlet_id,
                'delivery_to'       => $outlet_id,
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

            if(count($product_ids) > 0) {

                DB::beginTransaction();
                try{

                    for ($i=0; $i<count($product_ids); $i++) {

                        $purchaseProducts = Product::find($product_ids[$i]);
                        if(!empty($purchaseProducts)) {

                            // Expirable Product
                            if($purchaseProducts->is_expirable && count($product_expires_data[$purchaseProducts->id]) > 0) {

                                for($pe=0; $pe<count($product_expires_data[$purchaseProducts->id]); $pe++) {
                                    $stock_product = StockProduct::where('product_id', $product_expires_data[$purchaseProducts->id][$pe]['product_id'])->where('outlet_id', $outlet_id)->where('expires_date', $product_expires_data[$purchaseProducts->id][$pe]['expire_date'])->first();
                                    $stock_qty = $stock_product->stock_quantity ?? 0;
                                    if(empty($stock_product)) {
                                        $stock_new_inputs = [
                                            'product_id'    => $product_expires_data[$purchaseProducts->id][$pe]['product_id'],
                                            'outlet_id'         => $outlet_id,
                                            'in_stock_quantity' => $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                            'stock_quantity' => $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                            'expires_date' => $product_expires_data[$purchaseProducts->id][$pe]['expire_date'],
                                        ];

                                        $stock_new_products = StockProduct::create($stock_new_inputs);

                                    }else{
                                        $update_stock_input = [
                                            'in_stock_quantity' => $stock_product->in_stock_quantity + $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                            'stock_quantity'    => $stock_product->stock_quantity + $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],

                                        ];

                                        $stock_product->update($update_stock_input);
                                    }

                                    $stock_log_inputs[]    = [
                                        'product_id' => $product_expires_data[$purchaseProducts->id][$pe]['product_id'],
                                        'outlet_id' => $outlet_id,
                                        'in_stock_quantity' => $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity'],
                                        'stock_quantity'    => ($stock_qty + $product_expires_data[$purchaseProducts->id][$pe]['expire_quantity']),
                                        'expires_date'  => $product_expires_data[$purchaseProducts->id][$pe]['expire_date'],
                                        'stock_type'    => 'PR',
                                        'user_id'   => auth()->user()->id ?? 1,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }

//                                $product_expire_bulk_insert = ProductExpiresDate::insert($product_expires_data[$purchaseProducts->id]);

                            }
                            // Not Expirable Product
                            else{
                                $stock_product = StockProduct::where('product_id', $purchaseProducts->id)->where('outlet_id', $outlet_id)->first();

                                $stock_qty = $stock_product->stock_quantity ?? 0;
                                // Stock Not Exists
                                if(empty($stock_product)) {
                                    $new_stock_input = [
                                        'product_id'    => $purchaseProducts->id,
                                        'outlet_id'         => $outlet_id,
                                        'in_stock_quantity' => $product_array[$purchaseProducts->id]['receive_quantity'],
                                        'stock_quantity' => $product_array[$purchaseProducts->id]['receive_quantity'],
                                    ];

                                    $create_new_stock = StockProduct::create($new_stock_input);
                                }
                                // Stock is exists
                                else{
                                    $update_stock = [
                                        'in_stock_quantity' => $stock_product->in_stock_quantity + $product_array[$purchaseProducts->id]['receive_quantity'],
                                        'stock_quantity' => $stock_product->stock_quantity + $product_array[$purchaseProducts->id]['receive_quantity'],
                                    ];
                                    $stock_product->update($update_stock);
                                }

                                $stock_log_inputs[]    = [
                                    'product_id' => $purchaseProducts->id,
                                    'outlet_id' => $outlet_id,
                                    'in_stock_quantity' => $product_array[$purchaseProducts->id]['receive_quantity'],
                                    'stock_quantity'    => ($stock_qty + $product_array[$purchaseProducts->id]['receive_quantity']),
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

                                    $stock_product_gift = StockProductGift::where('product_id', $product_gifts_data[$purchaseProducts->id][$pg]['product_id'])->where('outlet_id', $request->get('outlet_id'))->where('gift_name', $product_gifts_data[$purchaseProducts->id][$pg]['gift_name'])->first();
                                    if(empty($stock_product_gift)) {
                                        $new_stock_gift_inputs = [
                                            'product_id'    => $product_gifts_data[$purchaseProducts->id][$pg]['product_id'],
                                            'outlet_id'         => $outlet_id,
                                            'gift_name' => $product_gifts_data[$purchaseProducts->id][$pg]['gift_name'],
                                            'stock_quantity'    => $product_gifts_data[$purchaseProducts->id][$pg]['gift_quantity'],
                                        ];

                                        $new_stock_gift = StockProductGift::create($new_stock_gift_inputs);

                                    }else{
                                        $update_stock_gift_inputs = [
                                            'stock_quantity'    => $stock_product_gift->stock_quantity + $product_gifts_data[$purchaseProducts->id][$pg]['gift_quantity'],
                                        ];

                                        $update_stock_gift = $stock_product_gift->update($update_stock_gift_inputs);
                                    }
                                }
                                //Gift Bulk Insert
//                                $gift_bulk_insert = ProductGift::insert($product_gifts_data[$purchaseProducts->id]);
                            }
                        }

                    }

                    if(count($stock_log_inputs) > 0) {
                        $stock_log_insert = StockProductsLog::insert($stock_log_inputs);
                    }

                    $purchase_receive_save = $this->purchaseReceiveRepository->create($receive_inputs);
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
//                    $receive_details_save = $purchase_receive_save->purchase_receive_details()->saveMany($receive_details_product_array);

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
            $store_requisition = StoreRequisition::find($purchase_order->store_requisition_id);
            $outlet_id = auth()->user()->outlet_id ?? $store_requisition->outlet_id;

            $products = json_decode($request->get('products'));

            $product_array = [];
            $product_receive_details = [];
            $rcv_product_details_ids = [];
            $stock_log_inputs = [];
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

                    if($product->id != "" && ($product->purchase_price != 0 && $product->purchase_price != "") && ($product->sale_price != 0 && $product->sale_price != "") && ($product->rcv_qty != 0 && $product->rcv_qty != "")) {

                        $product_amount = $product->rcv_qty * $product->purchase_price;
                        $free_amount = $product->free_qty * $product->purchase_price;
                        $product_rcv_amount = ($product_amount - $product->disc_amount) + 0;
                        $total_rcv_quantity = ($product->prcv_qty + $product->rcv_qty);

                        if($total_rcv_quantity >= $product->ord_qty) {
                            $prcv_status    = 2; // Full RCV
                            $rcv_product_details_ids[] = $product->order_details_id;
                        }else{
                            $prcv_status    = 1; //Partial RCV
                        }

                        $product_details_ids[]  = $product->order_details_id;
//                        $product_array[$product->order_details_id]    = [
//                            'receive_purchase_price'    => $product->purchase_price,
//                            'receive_quantity'          => $product->rcv_qty,
//                            'receive_discount_percent'  => $product->disc_percent,
//                            'receive_discount_amount'   => $product->disc_amount,
//                            'receive_free_quantity'     => $product->free_qty,
//                            'receive_free_amount'       => $free_amount,
//                            'receive_product_value'     => $product_amount,
//                            'receive_vat_amount'        => 0,
//                            'receive_amount'            => $product_rcv_amount,
//                            'receive_status'            => $prcv_status,
//                        ];

                        $product_array[$product->order_details_id]    = [
                            'receive_quantity'          => $total_rcv_quantity,
                            'receive_status'            => $prcv_status,
                        ];

                        $product_receive_details[] = [
                            'order_details_id'          => $product->order_details_id,
                            'receive_product_id'        => $product->id,
                            'receive_product_unit_id'   => $product->id,
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

                        $product_expires_data[$product->order_details_id] = [];
                        if($product->is_expirable && $product->is_expires && count($product->expires_data) > 0) {
                            foreach ($product->expires_data as $expires_data) {
                                if($expires_data->expire_date != "" && ($expires_data->expire_qty != 0 && $expires_data->expire_qty != "")) {
                                    $product_expires_data[$product->order_details_id][] = [
                                        'purchase_receive_details_id' => $product->order_details_id,
                                        'product_id' => $product->id,
                                        'expire_date' => $expires_data->expire_date,
                                        'expire_quantity' => $expires_data->expire_qty,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }
                            }
                        }

                        $product_gifts_data[$product->order_details_id] = [];
                        if($product->is_gifts && count($product->gifts) > 0) {
                            foreach ($product->gifts as $gift) {
                                if($gift->gift_name != "" && ($gift->gift_qty != 0 && $gift->gift_qty != "")) {
                                    $product_gifts_data[$product->order_details_id][] = [
                                        'purchase_receive_details_id' => $product->order_details_id,
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
                'outlet_id'         => $outlet_id,
                'delivery_to'       => $outlet_id,
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
                                    $stock_product = StockProduct::where('product_id', $product_expires_data[$purchaseReceiveDetails->id][$pe]['product_id'])->where('outlet_id', $outlet_id)->where('expires_date', $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_date'])->first();
                                    $stock_qty = $stock_product->stock_quantity ?? 0;
                                    if(empty($stock_product)) {
                                        $stock_new_inputs = [
                                            'product_id'    => $product_expires_data[$purchaseReceiveDetails->id][$pe]['product_id'],
                                            'outlet_id'         => $outlet_id,
                                            'in_stock_quantity' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                            'stock_quantity' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                            'expires_date' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_date'],
                                        ];

                                        $stock_new_products = StockProduct::create($stock_new_inputs);

                                    }else{
                                        $update_stock_input = [
                                            'in_stock_quantity' => $stock_product->in_stock_quantity + $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                            'stock_quantity'    => $stock_product->stock_quantity + $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],

                                        ];

                                        $stock_product->update($update_stock_input);
                                    }

                                    $stock_log_inputs[]    = [
                                        'product_id' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['product_id'],
                                        'outlet_id' => $outlet_id,
                                        'in_stock_quantity' => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity'],
                                        'stock_quantity'    => ($stock_qty + $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_quantity']),
                                        'expires_date'  => $product_expires_data[$purchaseReceiveDetails->id][$pe]['expire_date'],
                                        'stock_type'    => 'PR',
                                        'user_id'   => auth()->user()->id ?? 1,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                    ];
                                }

                                $product_expire_bulk_insert = ProductExpiresDate::insert($product_expires_data[$purchaseReceiveDetails->id]);

                            }
                            // Not Expirable Product
                            else{
                                $stock_product = StockProduct::where('product_id', $purchaseReceiveDetails->product_id)->where('outlet_id', $outlet_id)->first();

                                $stock_qty = $stock_product->stock_quantity ?? 0;
                                // Stock Not Exists
                                if(empty($stock_product)) {
                                    $new_stock_input = [
                                        'product_id'    => $purchaseReceiveDetails->product_id,
                                        'outlet_id'         => $outlet_id,
                                        'in_stock_quantity' => $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                        'stock_quantity' => $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                    ];

                                    $create_new_stock = StockProduct::create($new_stock_input);
                                }
                                // Stock is exists
                                else{
                                    $update_stock = [
                                        'in_stock_quantity' => $stock_product->in_stock_quantity + $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                        'stock_quantity' => $stock_product->stock_quantity + $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                    ];
                                    $stock_product->update($update_stock);
                                }

                                $stock_log_inputs[]    = [
                                    'product_id' => $purchaseReceiveDetails->product_id,
                                    'outlet_id' => $outlet_id,
                                    'in_stock_quantity' => $product_array[$purchaseReceiveDetails->id]['receive_quantity'],
                                    'stock_quantity'    => ($stock_qty + $product_array[$purchaseReceiveDetails->id]['receive_quantity']),
                                    'stock_type'    => 'PR',
                                    'user_id'   => auth()->user()->id ?? 1,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ];
                            }


                            // Add Product Gift and Gift Stock
                            if(count($product_gifts_data[$purchaseReceiveDetails->id]) > 0) {

                                for($pg=0; $pg<count($product_gifts_data[$purchaseReceiveDetails->id]); $pg++) {

                                    $stock_product_gift = StockProductGift::where('product_id', $product_gifts_data[$purchaseReceiveDetails->id][$pg]['product_id'])->where('outlet_id', $request->get('outlet_id'))->where('gift_name', $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_name'])->first();
                                    if(empty($stock_product_gift)) {
                                        $new_stock_gift_inputs = [
                                            'product_id'    => $product_gifts_data[$purchaseReceiveDetails->id][$pg]['product_id'],
                                            'outlet_id'         => $outlet_id,
                                            'gift_name' => $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_name'],
                                            'stock_quantity'    => $product_gifts_data[$purchaseReceiveDetails->id][$pg]['gift_quantity'],
                                        ];

                                        $new_stock_gift = StockProductGift::create($new_stock_gift_inputs);

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

                    if(count($stock_log_inputs) > 0) {
                        for($slg=0; $slg<count($stock_log_inputs); $slg++) {
                            $stock_log_inputs[$slg]['purchase_receive_id'] = $purchase_receive_save->id;
                        }
                        $stock_log_insert = StockProductsLog::insert($stock_log_inputs);
                    }
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

    /**
     * Display the specified PurchaseReceive.
     * GET|HEAD /purchaseReceives/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseReceive $purchaseReceive */
        $purchaseReceive = $this->purchaseReceiveRepository->find($id);

        if (empty($purchaseReceive)) {
            return $this->sendError('Purchase Receive not found');
        }

        $receive_product_data = [];
        $total_free_quantity = 0;
        $total_free_amount   = 0;
        if($purchaseReceive->purchase_order_id != 0) {

            if(count($purchaseReceive->receive_products) > 0)
            {
                foreach ($purchaseReceive->receive_products as $receive_product)
                {
                    $receive_product_data[] = [
                        'receive_details_id'    => $receive_product->id,
                        'product_id'    => $receive_product->product_id,
                        'product_unit_id'   => $receive_product->product_unit_id,
                        'product_name'  => $receive_product->product->product_name,
                        'product_code'  => $receive_product->product->product_code,
                        'cost_price'    => $receive_product->receive_purchase_price,
                        'depo_price'    => $receive_product->product->depo_price,
                        'mrp_price'     => $receive_product->product->mrp_price,
                        'purchase_measuring_unit'   => $receive_product->product->purchase_measuring_unit,
                        'sales_measuring_unit'  => $receive_product->product->sales_measuring_unit,
                        'purchase_unit' => $receive_product->product->purchase_unit->unit_code,
                        'sales_unit'    => $receive_product->product->sales_unit->sales_unit,
                        'carton_size'   => $receive_product->product->carton_size ?? 'N/A',
                        'order_quantity'       => $receive_product->order_quantity,
                        'receive_quantity'       => $receive_product->receive_quantity,

                    ];

                    $total_free_quantity += $receive_product->receive_free_quantity;
                    $total_free_amount += $receive_product->receive_purchase_price *             $receive_product->receive_free_quantity;
                }
            }
        }

        else{

            if(count($purchaseReceive->purchase_receive_details) > 0)
            {
                foreach ($purchaseReceive->purchase_receive_details as $receive_product)
                {
                    $receive_product_data[] = [
                        'receive_details_id'    => $receive_product->id,
                        'product_id'    => $receive_product->receive_product_id,
                        'product_unit_id'   => ($receive_product->receive_product_unit_id != 0) ? $receive_product->receive_product_unit_id : $receive_product->products->purchase_measuring_unit,
                        'product_name'  => $receive_product->products->product_name,
                        'product_code'  => $receive_product->products->product_code,
                        'cost_price'    => $receive_product->receive_purchase_price,
                        'depo_price'    => $receive_product->products->depo_price,
                        'mrp_price'     => $receive_product->receive_mrp_price,
                        'purchase_measuring_unit'   => $receive_product->products->purchase_measuring_unit,
                        'sales_measuring_unit'  => $receive_product->products->sales_measuring_unit,
                        'purchase_unit' => $receive_product->products->purchase_unit->unit_code,
                        'sales_unit'    => $receive_product->products->sales_unit->sales_unit,
                        'carton_size'   => $receive_product->products->carton_size ?? 'N/A',
                        'order_quantity'       => $receive_product->receive_order_quantity,
                        'receive_quantity'       => $receive_product->receive_quantity,
                        'free_quantity'       => $receive_product->receive_free_quantity,

                    ];

                    $total_free_quantity += $receive_product->receive_free_quantity;
                    $total_free_amount += $receive_product->receive_purchase_price *             $receive_product->receive_free_quantity;
                }
            }

        }

        $return_data    = [
            'receive_data'  =>  [
                'id'    => $purchaseReceive->id,
                'purchase_order_id'   => $purchaseReceive->purchase_order_id,
                'supplier_id'   => $purchaseReceive->supplier_id,
                'receive_type'   => $purchaseReceive->receive_type,
                'reference_no'   => $purchaseReceive->reference_no,
                'challan_no'   => $purchaseReceive->challan_no,
                'purchase_date'   => $purchaseReceive->purchase_date,
                'outlet_id'   => $purchaseReceive->outlet_id,
                'outlet_name'   => $purchaseReceive->outlets->name ?? '',
                'total_rcv_quantity'   => $purchaseReceive->total_rcv_quantity,
                'total_rcv_value'   => $purchaseReceive->total_rcv_value,
                'total_commission_value'   => $purchaseReceive->total_commission_value,
                'total_vat'   => $purchaseReceive->total_vat,
                'total_free_quantity'   => $total_free_quantity,
                'total_free_amount'   => $total_free_amount,
                'total_amount'   => $purchaseReceive->total_amount,
                'additional_discount'   => $purchaseReceive->additional_discount,
                'additional_cost'   => $purchaseReceive->additional_cost,
                'net_amount'   => $purchaseReceive->net_amount,
                'remarks'   => $purchaseReceive->remarks ?? 'N/A',
                'status'   => $purchaseReceive->status,
                'warehouse_id'   => $purchaseReceive->warehouse_id,
                'user_id'   => $purchaseReceive->user_id,
                'created_at'   => $purchaseReceive->created_at,
                'updated_at'   => $purchaseReceive->updated_at,
            ],
            'receive_product'  => $receive_product_data
        ];

        return $this->sendResponse($return_data, 'Purchase Receive retrieved successfully');
    }

    /**
     * Update the specified PurchaseReceive in storage.
     * PUT/PATCH /purchaseReceives/{id}
     *
     * @param int $id
     * @param UpdatePurchaseReceiveAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseReceiveAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReceive $purchaseReceive */
        $purchaseReceive = $this->purchaseReceiveRepository->find($id);

        if (empty($purchaseReceive)) {
            return $this->sendError('Purchase Receive not found');
        }

        $purchaseReceive = $this->purchaseReceiveRepository->update($input, $id);

        return $this->sendResponse($purchaseReceive->toArray(), 'PurchaseReceive updated successfully');
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
        /** @var PurchaseReceive $purchaseReceive */
//        $purchaseReceive = $this->purchaseReceiveRepository->find($id);
//
//        if (empty($purchaseReceive)) {
//            return $this->sendError('Purchase Receive not found');
//        }
//
//        $purchaseReceive->delete();
//
//        return $this->sendSuccess('Purchase Receive deleted successfully');
    }


    // Get Reference No
    public function getReferenceNo()
    {
        $outlet_id = auth()->user()->outlet_id ?? 1;
        $reference_no = $this->returnPurchaseReceiveReferenceNo('SR', $outlet_id);

        return $this->sendResponse([
            'reference_no' => $reference_no,
            'outlet_id' => $outlet_id
        ], 'Reference no get successfully');
    }

    // get supplier purchase order by supplier id
    public function supplierPurchaseOrder(Request $request)
    {
        $purchaseOrders = PurchaseOrder::where('supplier_id', $request->supplier_id)
                                        ->whereNotNull('store_requisition_id')
                                        ->where('receive_status', '!=', 1)
                                        ->where('approve_status', 1)
                                        ->get();

        $data   = PurchaseOrderResource::collection($purchaseOrders);


        return $this->sendResponse($data, "Purchase Order Retrieve Successfully");

    }

    // get purchase order products by purchase order id
    public function purchaseOrderProducts(Request $request)
    {
        if($request->purchase_order_id == "direct") {

            $supplier_products[] = [
                'id' => '',
                'name' => '',
                'code' => '',
                'unit_code' => 'pcs',
                'product_unit_id' => '',
                'purchase_price' => 0,
                'sale_price' => 0,
                'ord_qty' => 0,
                'prcv_qty' => 0,
                'rcv_qty' => 0,
                'free_qty' => 0,
                'free_amount' => 0,
                'disc_percent' => 0,
                'disc_amount' => 0,
                'amount' => 0,
                'is_expires' => false,
                'expires_data' => [],
                'is_gifts' => false,
                'gifts' => [],
                'is_expirable' => false,
            ];
//            if (count($products) > 0) {
//
//                foreach ($products as $purchase_product) {
//
//                    //if ($purchase_product->receive_quantity == 0) {
//                        $supplier_products[] = [
//                            'order_details_id' => $purchase_product->id,
//                            'id' => $purchase_product->id,
//                            'name' => $purchase_product->product_name,
//                            'code' => $purchase_product->product_code,
//                            'unit_code' => ($purchase_product->purchase_unit) ? $purchase_product->purchase_unit->unit_code : '',
//                            'carton_size' => $purchase_product->carton_size ?? '',
//                            'wh_stk' => 0,
//                            'last_po_qty' => 0,
//                            'last_purchase_qty' => 0,
//                            'po_qty' => 0,
//                            'remain_qty' => 0,
//                            'cpu' => $purchase_product->cost_price,
//                            'purchase_price' => $purchase_product->cost_price,
//                            'rcv_qty' => 0,
//                            'free_qty' => 0,
//                            'free_amount' => 0,
//                            'disc_percent' => 0,
//                            'disc_amount' => 0,
//                            'vat' => 0.000,
//                            'amount' => 0,
//                            'sale_price' => $purchase_product->mrp_price,
//                            'profit_percent_cpu' => 0,
//                            'profit_percent_mrp' => 0,
//                            'checked' => false,
//                            'is_expires' => false,
//                            'expires_data' => [],
//                            'is_gifts' => false,
//                            'gifts' => [],
//                            'is_expirable' => ($purchase_product->is_expirable == 1) ? true : false,
//                        ];
////                    }
//
//                }
//            }

            $return_data = [
//            'purchase_order' => new PurchaseOrderResource($purchaseOrder),
                'purchase_order' => [
                    'total_qty' => 0,
                    'total_value' => 0,
                    'commission_value' => 0,
                    'total_vat' => 0,
                    'total_free_amount' => 0,
                    'total_amount' => 0,
                    'additional_discount' => 0,
                    'additional_cost' => 0,
                    'net_amount' => 0,

                ],
                'purchase_products' => $supplier_products,
                'requisition_outlet_id' => '',
            ];

        }else {
            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = PurchaseOrder::find($request->purchase_order_id);

            if (empty($purchaseOrder)) {
                return $this->sendError('Purchase Order not found');
            }

            $store_requisition = '';
            if ($purchaseOrder->store_requisition_id) {
                $store_requisition = StoreRequisition::find($purchaseOrder->store_requisition_id);
            }

            $purchase_products = [];
            $supplier_products = [];
            if (count($purchaseOrder->purchase_products) > 0) {

                $purchase_order_products = $purchaseOrder->purchase_products()->where('receive_status', '!=', 2)->get();
                foreach ($purchase_order_products as $purchase_product) {
                    $purchase_products[$purchase_product->product_id] = [

                        'product_unit_id' => $purchase_product->product_unit_id,
                        'purchase_price' => $purchase_product->order_purchase_price,
                        'order_quantity' => $purchase_product->order_quantity,
                        'receive_quantity' => $purchase_product->receive_quantity,
                        'discount_percent' => $purchase_product->order_discount_percent,
                        'free_quantity' => $purchase_product->order_free_quantity,
                        'product_value' => $purchase_product->order_product_value,
                        'discount_amount' => $purchase_product->order_discount_amount,
                        'free_amount' => $purchase_product->order_free_amount,
                        'vat_amount' => $purchase_product->order_vat_amount,
                        'amount' => $purchase_product->order_amount,
                        'line_notes' => $purchase_product->order_line_notes,
                        'checked' => true,

                    ];

                    if ($purchase_product->receive_quantity == 0 || $purchase_product->receive_quantity < $purchase_product->order_quantity) {
                        $supplier_products[] = [
                            'order_details_id' => $purchase_product->id,
                            'id' => $purchase_product->product_id,
                            'name' => $purchase_product->product->product_name,
                            'code' => $purchase_product->product->product_code,
                            'unit_code' => $purchase_product->product->purchase_unit->unit_code,
                            'product_unit_id'   => $purchase_product->product_unit_id,
                            'cpu' => ($purchase_product->order_purchase_price != 0) ? $purchase_product->order_purchase_price : $purchase_product->product->cost_price,
                            'purchase_price' => ($purchase_product->order_purchase_price != 0) ? $purchase_product->order_purchase_price : $purchase_product->product->cost_price,
                            'sale_price' => $purchase_product->product->mrp_price,
                            'ord_qty' => $purchase_product->order_quantity,
                            'prcv_qty' => $purchase_product->receive_quantity,
                            'rcv_qty' => 0,
                            'free_qty' => $purchase_product->order_free_quantity,
                            'free_amount' => $purchase_product->order_free_amount,
                            'disc_percent' => $purchase_product->order_discount_percent,
                            'disc_amount' => $purchase_product->order_discount_amount,
                            'amount' => $purchase_product->order_amount,
                            'is_expires' => false,
                            'expires_data' => [],
                            'is_gifts' => false,
                            'gifts' => [],
                            'is_expirable' => ($purchase_product->product->is_expirable == 1) ? true : false,
                        ];
                    }

                }
            }

            $return_data = [
//            'purchase_order' => new PurchaseOrderResource($purchaseOrder),
                'purchase_order' => [
                    'total_qty' => $purchaseOrder->total_qty,
                    'total_value' => $purchaseOrder->total_value,
                    'commission_value' => $purchaseOrder->commission_value,
                    'total_vat' => $purchaseOrder->total_vat,
                    'total_free_amount' => $purchaseOrder->total_free_amount,
                    'total_amount' => $purchaseOrder->total_amount,
                    'additional_discount' => 0,
                    'additional_cost' => 0,
                    'net_amount' => $purchaseOrder->total_amount,

                ],
                'purchase_products' => $supplier_products,
                'requisition_outlet_id' => $store_requisition ? $store_requisition->outlet_id : '',
            ];
        }

        return $this->sendResponse($return_data, 'Purchase Order retrieved successfully');

    }


    /** Purchase Receive Bulk Upload */
    public function purchaseReceiveBulkUpload(Request $request, PurchaseReceiveProductImport $receiveProductImport)
    {
        $this->validate($request, [
            'excel_file'    => 'required',
        ]);

        $file = $request->file('excel_file');

        $import = $receiveProductImport->import($file);
//        $import = new PurchaseReceiveProductImport();
//        $import->import($file);

//        return response()->json($receiveProductImport->detailsProductData);

        if($import){
            return $this->sendResponse($import, 'Bulk Purchase Receive Successfully Done!');
        }else{
            return $this->sendResponse($import, 'Something went wrong, please try again!');
        }
    }

    /** Purchase Receive With Purchase Receive Board */
    public function purchaseReceiveWithBoard(Request $request)
    {
        $outlet_id = auth()->user()->outlet_id ?? 1;
        $products = json_decode($request->get('products'));

        $product_array = [];
        $supplier_array = [];
        $supplier_product_array = [];
        if(count($products) > 0)
        {
            foreach ($products as $product) {

                $supplier_id = $product->supplier_id;
                $product_id  = $product->product_id;
                $rcv_qty    = $product->rcv_qty;
                $tp = $product->tp;
                $mrp = $product->mrp;

                if($supplier_id != ""
                    && $product_id != ""
                    && ($rcv_qty != 0 || $rcv_qty != "")
                    && ($tp != 0 || $tp != "")
                    && ($mrp != 0 || $mrp != "")
                ) {
                    $product_array[]    = $product;

                    if(!in_array($supplier_id, $supplier_array)) {
                        $supplier_array[]   = $supplier_id;
                    }

                    $supplier_product_array[$supplier_id][] = $product;
                }

            }
        }else{
            return $this->sendError('Product Item Not Found!');
        }


        if(count($product_array) > 0) {

            DB::beginTransaction();
            try{

                $single_product_array = [];
                $product_expires_array = [];
                $product_receive_details_inputs = [];
                $stock_log_insert_inputs = [];
                $supplier_ledger_inputs = [];
                $total_rcv_qty = 0;
                $total_rcv_value = 0;
                $total_free_amount = 0;
                for($p=0; $p<count($product_array); $p++) {
                    $product_expiry_date = $product_array[$p]->expiry_date;
                    $supplier_id = $product_array[$p]->supplier_id;
                    $product_id = $product_array[$p]->product_id;
                    $order_quantity = $product_array[$p]->order_qty;
                    $free_quantity  = $product_array[$p]->free_qty;
                    $receive_quantity   = $product_array[$p]->rcv_qty;
                    $purchase_price = $product_array[$p]->tp;
                    $sale_price = $product_array[$p]->mrp;

                    $total_rcv_qty += $receive_quantity;
                    $total_rcv_value += ($receive_quantity * $purchase_price);
                    $total_free_amount += ($free_quantity * $sale_price);

                    // For product receive details data
                    if(!array_key_exists($product_id, $single_product_array)) {
                        $single_product_array[$product_id] = [
                            'receive_product_id' => $product_id,
                            'receive_supplier_id' => $supplier_id,
                            'receive_purchase_price' => $purchase_price,
                            'receive_mrp_price' => $sale_price,
                            'receive_quantity' => $receive_quantity,
                            'receive_free_quantity' => $free_quantity,
                            'receive_free_amount' => ($free_quantity * $purchase_price),
                            'receive_product_value' => ($receive_quantity * $purchase_price),
                            'receive_amount' => ($receive_quantity * $purchase_price),
                        ];
                    }else{
                        $old_rcv_qty = $single_product_array[$product_id]['receive_quantity'];
                        $old_free_qty = $single_product_array[$product_id]['receive_free_quantity'];
                        $old_free_amount = $single_product_array[$product_id]['receive_free_amount'];
                        $old_product_value = $single_product_array[$product_id]['receive_product_value'];
                        $old_rcv_amount = $single_product_array[$product_id]['receive_amount'];

                        $single_product_array[$product_id]['receive_quantity'] = ($old_rcv_qty + $receive_quantity);
                        $single_product_array[$product_id]['receive_free_quantity'] = ($old_free_qty + $free_quantity);
                        $single_product_array[$product_id]['receive_free_amount'] = ($old_free_amount + ($free_quantity * $purchase_price));
                        $single_product_array[$product_id]['receive_product_value'] = ($old_product_value + ($receive_quantity * $purchase_price));
                        $single_product_array[$product_id]['receive_amount'] = ($old_rcv_amount + ($receive_quantity * $purchase_price));
                    }


                    // Purchase Receive With Expiry Date
                    if(!empty($product_expiry_date)) {
                        $expiry_date = date("Y-m-d", strtotime($product_expiry_date));
                        $stock_product = StockProduct::where('product_id', $product_id)
                                                        ->where('outlet_id', $outlet_id)
                                                        ->whereDate('expires_date', $expiry_date)
                                                        ->first();

                        $old_stock_quantity = $stock_product->stock_quantity ?? 0;
                        $old_in_stock_quantity = $stock_product->in_stock_quantity ?? 0;

                        if(!empty($stock_product)) {

                            $update_in_stock_qty = ($old_in_stock_quantity + $receive_quantity);
                            $update_stock_qty   = ($old_stock_quantity + $receive_quantity);
                            $update_stock_inputs = [
                                'in_stock_quantity' => $update_in_stock_qty,
                                'stock_quantity'    => $update_stock_qty,
                            ];
                            $stock_update = $stock_product->update($update_stock_inputs);

                        }else{
                            $stock_new_inputs = [
                                'product_id'    => $product_id,
                                'outlet_id'         => $outlet_id,
                                'in_stock_quantity' => $receive_quantity,
                                'stock_quantity' => $receive_quantity,
                                'expires_date' => $expiry_date,
                            ];

                            $stock_new_products = StockProduct::create($stock_new_inputs);
                        }

                        $stock_log_insert_inputs[] = [
                            'product_id'    => $product_id,
                            'outlet_id'     => $outlet_id,
                            'in_stock_quantity' => $receive_quantity,
                            'stock_quantity'    => ($old_stock_quantity + $receive_quantity),
                            'out_stock_quantity'    => 0,
                            'expires_date'  => $expiry_date,
                            'stock_type'    => 'PR',
                            'user_id'   => auth()->user()->id ?? 1,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];

                        $product_expires_array[$product_id][]    = [
                            'product_id'    => $product_id,
                            'expire_date'   => $expiry_date,
                            'expire_quantity'      => $receive_quantity,
                            'created_at'    => Carbon::now()->toDateTimeString(),
                            'updated_at'    => Carbon::now()->toDateTimeString(),
                        ];
                    }
                    // Purchase Receive Without Expiry Date
                    else{
                        $stock_product = StockProduct::where('product_id', $product_id)
                            ->where('outlet_id', $outlet_id)
                            ->first();

                        $old_stock_quantity = $stock_product->stock_quantity ?? 0;
                        $old_in_stock_quantity = $stock_product->in_stock_quantity ?? 0;

                        if(!empty($stock_product)) {

                            $update_in_stock_qty = ($old_in_stock_quantity + $receive_quantity);
                            $update_stock_qty   = ($old_stock_quantity + $receive_quantity);
                            $update_stock_inputs = [
                                'in_stock_quantity' => $update_in_stock_qty,
                                'stock_quantity'    => $update_stock_qty,
                            ];
                            $stock_update = $stock_product->update($update_stock_inputs);

                        }else{
                            $stock_new_inputs = [
                                'product_id'    => $product_id,
                                'outlet_id'         => $outlet_id,
                                'in_stock_quantity' => $receive_quantity,
                                'stock_quantity' => $receive_quantity,
                            ];

                            $stock_new_products = StockProduct::create($stock_new_inputs);
                        }

                        $stock_log_insert_inputs[] = [
                            'product_id'    => $product_id,
                            'outlet_id'     => $outlet_id,
                            'in_stock_quantity' => $receive_quantity,
                            'stock_quantity'    => ($old_stock_quantity + $receive_quantity),
                            'out_stock_quantity'    => 0,
                            'expires_date'  => NULL,
                            'stock_type'    => 'PR',
                            'user_id'   => auth()->user()->id ?? 1,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];


                    }

                }

                // Array Setup for purchase receive details
                if(count($single_product_array) > 0) {
                    foreach($single_product_array as $key => $value ) {
                        $product_receive_details_inputs[] = $value;
                    }
                }

                // Array Setup for supplier ledger
                if(count($supplier_array) > 0) {
                    for($i=0; $i<count($supplier_array); $i++) {
                        $supplier_id    = $supplier_array[$i];

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
                                $product_qty    = $supplier_product_array[$supplier_id][$j]->rcv_qty;
                                $product_purchase_price = $supplier_product_array[$supplier_id][$j]->tp;
                                $product_free_qty = $supplier_product_array[$supplier_id][$j]->free_qty;

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
                    }
                }

                // Array For purchase receive master data
                $receive_inputs = [
                    'purchase_order_id' => 0,
                    'supplier_id'       => 0,
                    'receive_type'      => 'SR',
                    'reference_no'      => $this->returnPurchaseReceiveReferenceNo("SR", $outlet_id),
                    //'challan_no'      => '',
                    'purchase_date'     => date("Y-m-d"),
                    'outlet_id'         => $outlet_id,
                    'delivery_to'       => $outlet_id,
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

                DB::commit();
                return $this->sendSuccess("Purchase Receive Successfully Done!");
            }catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }else{
            return $this->sendError("Please fill up required product item data");
        }

    }


}
