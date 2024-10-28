<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderAPIRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\Company;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\OrderRequisitionDetail;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Support\Facades\File;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class PurchaseOrderController
 * @package App\Http\Controllers\API
 */

class PurchaseOrderAPIController extends AppBaseController
{
    /** @var  PurchaseOrderRepository */
    private $purchaseOrderRepository;

    public function __construct(PurchaseOrderRepository $purchaseOrderRepo)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepo;
    }

    /**
     * Display a listing of the PurchaseOrder.
     * GET|HEAD /purchaseOrders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    { 

        $purchaseOrders = PurchaseOrder::orderBy('id', 'desc')->get();

        $return_data    = PurchaseOrderResource::collection($purchaseOrders);

        return $this->sendResponse($return_data, 'Purchase Orders retrieved successfully');
    }


    public function getPurchaseOrdersData()
    {
        $purchaseOrders = PurchaseOrder::where('approve_status', 0)->orderBy('id', 'desc')->get();

        $return_data = PurchaseOrderResource::collection($purchaseOrders);
        return $this->sendResponse($return_data, 'Purchase Orders retrieved successfully');
    }

    /**
     * Store a newly created PurchaseOrder in storage.
     * POST /purchaseOrders
     *
     * @param CreatePurchaseOrderAPIRequest $request
     *
     * @return Response
     */
        //    public function store(CreatePurchaseOrderAPIRequest $request)
        //    {
        //        // For Purchase Products
        //        $products = json_decode($request->products);
        //
        //        //return gettype($request->get('is_single_po'));
        //
        //        if($request->get('is_single_po') == "true") {
        //
        //            $this->validate($request, [
        //                'supplier_id'   => 'required',
        //                'order_date'    => 'required',
        ////            'delivery_date' => 'required',
        //                'reference_no'  => 'required',
        ////            'start_date'    => 'required',
        ////            'end_date'      => 'required',
        //            ]);
        //
        //            $product_array = [];
        //            $total_quantity = 0;
        //            $total_product_value = 0;
        //            $total_free_amount = 0;
        //            $total_discount_amount = 0;
        //            $total_product_amount = 0;
        //            $total_vat_amount = 0;
        //            if(count($products) > 0) {
        //
        //                foreach($products as $product) {
        //
        ////                if($product->checked) {
        //                    if(($product->purchase_price > 0 && $product->purchase_price != '') && ($product->qty > 0 && $product->qty != '')) {
        //
        //                        $product_value = ($product->purchase_price * $product->qty);
        //                        $free_amount = ($product->purchase_price * $product->free_qty);
        //                        $discount_amount = ($product_value * $product->disc_percent) / 100;
        //                        $product_amount = $product_value - $discount_amount;
        //
        //                        $product_array[]    = new OrderRequisitionDetail([
        //                            'product_id'    => $product->id,
        //                            'product_unit_id'    => $product->product_unit_id,
        //                            'order_purchase_price'    => $product->purchase_price,
        //                            'order_quantity'   => $product->qty,
        //                            'order_discount_percent'  => $product->disc_percent,
        //                            'order_free_quantity'  => $product->free_qty,
        //                            'order_product_value'  => $product_value,
        //                            'order_discount_amount'  => $discount_amount,
        //                            'order_free_amount'  => $free_amount,
        //                            'order_vat_amount'  => $product->vat,
        //                            'order_amount'    => $product_amount,
        ////                        'order_line_notes'    => $product->line_notes,
        //                        ]);
        //
        //                        $total_quantity += $product->qty;
        //                        $total_product_value += $product_value;
        //                        $total_discount_amount += $discount_amount;
        //                        $total_free_amount  += $free_amount;
        //                        $total_product_amount += $product_amount;
        //                        $total_vat_amount   += $product->vat;
        //                    }
        //                }
        //            }
        //
        //            if (empty($product_array)) {
        ////            return $this->sendError('Please select/checked at list one products item');
        //                return $this->sendError('Please fill up at list one products item order qty getter then 0');
        //            }
        //
        //            $inputs = [
        //                'reference_no'  => $request->get('reference_no'),
        //                'supplier_id'   => $request->get('supplier_id'),
        //                'supplier_payment_type' => $request->get('supplier_payment_type'),
        //                'number_of_po' => $request->get('number_of_po'),
        //                'supply_schedule' => $request->get('supply_schedule'),
        //                'order_date' => customDateFormat($request->get('order_date')),
        //                'delivery_date' => customDateFormat($request->get('delivery_date')),
        //                'warehouse_id' => $request->get('warehouse_id') ?? 0,
        //                'delivery_to_outlet' => $request->get('outlet_id') ?? 0,
        //                'start_date' => $request->get('start_date'),
        //                'end_date' => $request->get('end_date'),
        //                'total_qty' => $total_quantity,
        //                'total_value' => $total_product_value,
        //                'commission_value' => $total_discount_amount,
        //                'total_vat' => $total_vat_amount,
        //                'total_free_amount' => $total_free_amount,
        //                'total_amount' => $total_product_amount,
        //            ];
        //
        //            DB::beginTransaction();
        //            try {
        //                $purchaseOrder = $this->purchaseOrderRepository->create($inputs);
        //                $purchase_product = $purchaseOrder->purchase_products()->saveMany($product_array);
        //                DB::commit();
        //
        //                return $this->sendResponse($purchaseOrder->toArray(), 'Purchase Order saved successfully');
        //            }catch (\Exception $e){
        //                DB::rollBack();
        //                return $this->sendError($e->getMessage());
        //            }
        //        }
        //        // For Multiple PO
        //        else{
        //
        //            $this->validate($request, [
        ////                'supplier_id'   => 'required',
        //                'order_date'    => 'required',
        ////            'delivery_date' => 'required',
        //                'reference_no'  => 'required',
        ////            'start_date'    => 'required',
        ////            'end_date'      => 'required',
        //            ]);
        //
        //            $supplier_array = [];
        //            $supplier_product_array = [];
        //            if(count($products) > 0) {
        //                foreach ($products as $product) {
        //                    if(($product->purchase_price > 0 && $product->purchase_price != '') && ($product->qty > 0 && $product->qty != '') && ($product->supplier_id != '' && $product->supplier_id != 0)) {
        //
        //                        if(!in_array($product->supplier_id, $supplier_array)) {
        //                            $supplier_array[]   = $product->supplier_id;
        //                        }
        //                        $supplier_product_array[$product->supplier_id][] = $product;
        //
        //                    }
        //                }
        //            }
        //
        //            if(empty($supplier_product_array)) {
        //                return $this->sendError('Product not available for purchase order');
        //            }
        //
        //            if(count($supplier_array) > 0) {
        //                DB::beginTransaction();
        //                try{
        //                    for($s=0; $s<count($supplier_array); $s++) {
        //                        $total_quantity = 0;
        //                        $total_product_value = 0;
        //                        $total_free_amount = 0;
        //                        $total_discount_amount = 0;
        //                        $total_product_amount = 0;
        //                        $total_vat_amount = 0;
        //                        $order_product_array    = [];
        //                        foreach ($supplier_product_array[$supplier_array[$s]] as $supplier_product) {
        //                            $product_value = ($supplier_product->purchase_price * $supplier_product->qty);
        //                            $free_amount = ($supplier_product->purchase_price * $supplier_product->free_qty);
        //                            $discount_amount = ($product_value * $supplier_product->disc_percent) / 100;
        //                            $product_amount = $product_value - $discount_amount;
        //
        //                            $order_product_array[]    = new OrderRequisitionDetail([
        //                                'product_id'    => $supplier_product->id,
        //                                'product_unit_id'    => $supplier_product->product_unit_id,
        //                                'order_purchase_price'    => $supplier_product->purchase_price,
        //                                'order_quantity'   => $supplier_product->qty,
        //                                'order_discount_percent'  => $supplier_product->disc_percent,
        //                                'order_free_quantity'  => $supplier_product->free_qty,
        //                                'order_product_value'  => $product_value,
        //                                'order_discount_amount'  => $discount_amount,
        //                                'order_free_amount'  => $free_amount,
        //                                'order_vat_amount'  => $supplier_product->vat,
        //                                'order_amount'    => $product_amount,
        ////                                'order_line_notes'    => $product->line_notes,
        //                            ]);
        //
        //                            $total_quantity += $supplier_product->qty;
        //                            $total_product_value += $product_value;
        //                            $total_discount_amount += $discount_amount;
        //                            $total_free_amount  += $free_amount;
        //                            $total_product_amount += $product_amount;
        //                            $total_vat_amount   += $supplier_product->vat;
        //                        }
        //
        //
        //                        $inputs = [
        //                            'reference_no'  => $request->get('reference_no'),
        //                            'supplier_id'   => $supplier_array[$s],
        //                            'supplier_payment_type' => $request->get('supplier_payment_type'),
        //                            'number_of_po' => $request->get('number_of_po'),
        //                            'supply_schedule' => $request->get('supply_schedule'),
        //                            'order_date' => customDateFormat($request->get('order_date')),
        //                            'delivery_date' => customDateFormat($request->get('delivery_date')),
        //                            'warehouse_id' => $request->get('warehouse_id') ?? 0,
        //                            'delivery_to_outlet' => $request->get('outlet_id') ?? 0,
        //                            'start_date' => $request->get('start_date'),
        //                            'end_date' => $request->get('end_date'),
        //                            'total_qty' => $total_quantity,
        //                            'total_value' => $total_product_value,
        //                            'commission_value' => $total_discount_amount,
        //                            'total_vat' => $total_vat_amount,
        //                            'total_free_amount' => $total_free_amount,
        //                            'total_amount' => $total_product_amount,
        //                        ];
        //
        //                        $purchaseOrder = $this->purchaseOrderRepository->create($inputs);
        //                        $purchase_product = $purchaseOrder->purchase_products()->saveMany($order_product_array);
        //
        //                    }
        //
        //                    DB::commit();
        //                    return $this->sendSuccess('Purchase Orders Generate Successfully!');
        //                }catch(\Exception $e){
        //                    DB::rollBack();
        //                    return $this->sendError($e->getMessage());
        //                }
        //
        //
        //            }else{
        //                return $this->sendError('Please Select Supplier for all order product');
        //            }
        //
        //        }
        //    }

    public function store(CreatePurchaseOrderAPIRequest $request)
    {

        if($request->get('is_single_po') == "true") {
            // For Purchase Products
            $products = json_decode($request->products);
            $this->validate($request, [
                'supplier_id'   => 'required',
                'order_date'    => 'required',
                'reference_no'  => 'required',
                'supplier_payment_type' => 'required',
                'delivery_date' => 'required'
            ]);

            $product_array = [];
            $total_quantity = 0;
            $total_product_value = 0;
            $total_free_amount = 0;
            $total_discount_amount = 0;
            $total_product_amount = 0;
            $total_vat_amount = 0;
            if(count($products) > 0) {

                foreach($products as $product) {

            //   if($product->checked) {
                    if($product->product_id != "" && ($product->tp > 0 && $product->tp != '') && ($product->order_qty > 0 && $product->order_qty != '')) {

                        $product_value = ($product->tp * $product->order_qty);
                    //      $free_amount = ($product->tp * $product->free_qty);
                        $free_amount = 0;
                    //     $discount_amount = ($product_value * $product->disc_percent) / 100;
                        $discount_amount = ($product_value * 0) / 100;
                        $product_amount = $product_value - $discount_amount;

                        $product_array[]    = new OrderRequisitionDetail([
                            'product_id'    => $product->product_id,
                            'product_unit_id'    => $product->product_unit_id,
                            'order_purchase_price'    => $product->tp,
                            'order_quantity'   => $product->order_qty,
                            'order_discount_percent'  => 0,
                            'order_free_quantity'  => 0,
                            'order_product_value'  => $product_value,
                            'order_discount_amount'  => $discount_amount,
                            'order_free_amount'  => $free_amount,
                            'order_vat_amount'  => 0,
                            'order_amount'    => $product_amount,
                        //   'order_line_notes'    => $product->line_notes,
                        ]);

                        $total_quantity += $product->order_qty;
                        $total_product_value += $product_value;
                        $total_discount_amount += $discount_amount;
                        $total_free_amount  += $free_amount;
                        $total_product_amount += $product_amount;
                        $total_vat_amount   += 0;
                    }
                }
            }

            if (empty($product_array)) {
//            return $this->sendError('Please select/checked at list one products item');
                return $this->sendError('Please fill up at list one products item order qty getter then 0');
            }

            $inputs = [
                'reference_no'  => $request->get('reference_no'),
                'supplier_id'   => $request->get('supplier_id'),
                'supplier_payment_type' => $request->get('supplier_payment_type'),
                'number_of_po' => $request->get('number_of_po'),
                'supply_schedule' => $request->get('supply_schedule'),
                'order_date' => customDateFormat($request->get('order_date')),
                'delivery_date' => customDateFormat($request->get('delivery_date')),
                'warehouse_id' => $request->get('warehouse_id') ?? 0,
                'delivery_to_outlet' => $request->get('outlet_id') ?? 0,
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'total_qty' => $total_quantity,
                'total_value' => $total_product_value,
                'commission_value' => $total_discount_amount,
                'total_vat' => $total_vat_amount,
                'total_free_amount' => $total_free_amount,
                'total_amount' => $total_product_amount,
                'remarks' => $request->get('remarks'),
            ];

            DB::beginTransaction();
            try {
                $purchaseOrder = $this->purchaseOrderRepository->create($inputs);
                $purchase_product = $purchaseOrder->purchase_products()->saveMany($product_array);
                DB::commit();
                return $this->sendResponse($purchaseOrder->toArray(), 'Purchase Order saved successfully');
            }catch (\Exception $e){
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }
        // For Multiple PO
        else{

            $order_data = json_decode($request->get('purchase_orders'));

            if(count($order_data) > 0) {

                $submit_status = true;
                foreach($order_data as $order){
                    if($order->supplier_payment_type == '') {
                        $submit_status = false;
                    }
                }

                if($submit_status == false) {
                    return $this->sendError("Supplier payment type can't be null for single one");
                }

                DB::beginTransaction();
                try{
                    foreach ($order_data as $order){
                        $order_product_array    = [];
                        foreach ($order->order_products as $supplier_product) {
                            $product_value = ($supplier_product->tp * $supplier_product->order_qty);
//                            $free_amount = ($supplier_product->purchase_price * $supplier_product->free_qty);
                            $free_amount = ($supplier_product->tp * 0);
//                            $discount_amount = ($product_value * $supplier_product->disc_percent) / 100;
                            $discount_amount = ($product_value * 0) / 100;
                            $product_amount = $product_value - $discount_amount;

                            $order_product_array[]    = new OrderRequisitionDetail([
                                'product_id'    => $supplier_product->product_id,
                                'product_unit_id'    => $supplier_product->product_unit_id,
                                'order_purchase_price'    => $supplier_product->tp,
                                'order_quantity'   => $supplier_product->order_qty,
                                'order_discount_percent'  => 0,
                                'order_free_quantity'  => 0,
                                'order_product_value'  => $product_value,
                                'order_discount_amount'  => $discount_amount,
                                'order_free_amount'  => $free_amount,
                                'order_vat_amount'  => 0,
                                'order_amount'    => $product_amount,
//                                'order_line_notes'    => $product->line_notes,
                            ]);

                        }


                        $inputs = [
                            'reference_no'  => $order->reference_no,
                            'supplier_id'   => $order->supplier_id,
                            'supplier_payment_type' => $order->supplier_payment_type,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,
                            'warehouse_id' => $order->warehouse_id,
                            'delivery_to_outlet' => $order->delivery_to_outlet,
                            'total_qty' => $order->total_qty,
                            'total_value' => $order->total_value,
                            'commission_value' => $order->commission_value,
                            'total_vat' => $order->total_vat,
                            'total_free_amount' => $order->total_free_amount,
                            'total_amount' => $order->total_amount,
                        ];

                        $purchaseOrder = $this->purchaseOrderRepository->create($inputs);
                        $purchase_product = $purchaseOrder->purchase_products()->saveMany($order_product_array);

                    }

                    DB::commit();
                    return $this->sendSuccess('Purchase Orders Generate Successfully!');
                }catch(\Exception $e){
                    DB::rollBack();
                    return $this->sendError($e->getMessage());
                }


            }else{
                return $this->sendError('Order data not found, Please try again!');
            }

        }
    }

    /**
     * Display the specified PurchaseOrder.
     * GET|HEAD /purchaseOrders/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchase_products = [];
        $supplier_products = [];
        if (count($purchaseOrder->purchase_products) > 0) {
            foreach ($purchaseOrder->purchase_products as $purchase_product) {
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


                $product = Product::where('id', $purchase_product->product_id)->first();
                $unit_data  = Unit::where('id', $purchase_product->product_unit_id)->first();
                $supplier_products[] = [
                    'supplier_id' => '',
                    'product_id' => $purchase_product->product_id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'unit_code' => $unit_data->unit_code,
                    'product_unit_id' => $purchase_product->product_unit_id,
                    'order_qty' => $purchase_product->order_quantity,
                    'tp' => $purchase_product->order_purchase_price,
                    'mrp' => $product->mrp_price,
                    'amnt' => ($purchase_product->order_quantity * $purchase_product->order_purchase_price),
                    'disabled' => false,
                ];
            }
        }

        $supplier = Supplier::find($purchaseOrder->supplier_id);
        $products = Product::where('status', 1)->get();

//        if (count($products) > 0) {
//            foreach ($products as $product) {
//                $supplier_products[] = [
//                    'id' => $product->id,
//                    'name' => $product->product_name,
//                    'code' => $product->product_code,
//                    'unit_code' => $product->purchase_unit->unit_code,
//                    'product_unit_id'   => $product->purchase_measuring_unit,
//                    'carton_size' => $product->carton_size ?? '',
//                    'reorder_qty' => 0,
//                    'min_stk_qty' => 0,
//                    'wh_stk' => 0,
//                    'str_stk' => 1,
//                    'ttl_stk' => 1,
//                    'last_po' => 0,
//                    'last_purchase' => 1,
//                    'in_queue' => 0,
//                    'last_free_qty' => 0,
//                    'last_discount_percent' => 0,
//                    'last_purchase_price' => 65,
//                    'last_mrp' => 80,
//                    'last_3month_avg_sale' => 0,
//                    'last_month_sale' => 0,
//                    'last_week_sale' => 0,
//                    'sale_qty' => 0,
//                    'status' => 'false',
//                    'purchase_price' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['purchase_price'] : $product->cost_price,
//                    'mrp' => $product->mrp_price,
//                    'qty' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['order_quantity'] : 0,
//                    'disc_percent' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['discount_percent'] : 0,
//                    'free_qty' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['free_quantity'] : 0,
//                    'value' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['product_value'] : 0.000,
//                    'disc_amount' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['discount_amount'] : 0,
//                    'free_amount' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['free_amount'] : 0,
//                    'vat' => 0.000,
//                    'amount' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['amount'] : 0.00,
//                    'last_profit_percent_cpu' => 0,
//                    'profit_percent_cpu' => 0,
//                    'profit_percent_mrp' => 0,
//                    'line_notes' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['line_notes'] : '',
////                    'checked' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['checked'] : false,
//                    'checked' => (key_exists($product->id, $purchase_products)) ? $purchase_products[$product->id]['checked'] : true,
//                ];
//            }
//        }


        $return_data    = [
            'purchase_order' => new PurchaseOrderResource($purchaseOrder),
            'purchase_products'  => $supplier_products,
        ];

        return $this->sendResponse($return_data, 'Purchase Order retrieved successfully');
    }

    public function viewDetails($id) {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $order_product_data = [];
        $total_free_quantity = 0;
        $total_free_amount   = 0;
        if(count($purchaseOrder->purchase_products) > 0)
        {
            foreach ($purchaseOrder->purchase_products as $order_details)
            {
                $order_product_data[] = [
                    'receive_details_id'    => $order_details->id,
                    'product_id'    => $order_details->product_id,
                    'product_unit_id'   => $order_details->product_unit_id,
                    'product_name'  => $order_details->product->product_name,
                    'product_code'  => $order_details->product->product_code,
                    'cost_price'    => $order_details->order_purchase_price,
                    'depo_price'    => $order_details->product->depo_price,
                    'mrp_price'     => $order_details->product->mrp_price,
                    'purchase_measuring_unit'   => $order_details->product->purchase_measuring_unit,
                    'sales_measuring_unit'  => $order_details->product->sales_measuring_unit,
                    'purchase_unit' => $order_details->product->purchase_unit->unit_code ?? '',
                    'sales_unit'    => $order_details->product->sales_unit->unit_code ?? '',
                    'carton_size'   => $order_details->product->carton_size ?? 'N/A',
                    'order_quantity'       => $order_details->order_quantity,
                    'order_discount_amount'       => $order_details->order_discount_amount,
                    'order_free_quantity'       => $order_details->order_free_quantity,
                    'order_free_amount'       => $order_details->order_free_amount,
                    'order_vat_amount'       => $order_details->order_vat_amount,
                    'order_amount'       => ($order_details->order_amount != 0) ? $order_details->order_amount : (($order_details->order_purchase_price * $order_details->order_quantity) - $order_details->order_discount_amount) + $order_details->order_vat_amount,

                ];

                $total_free_quantity += $order_details->order_free_quantity;
                $total_free_amount += $order_details->order_purchase_price *             $order_details->order_free_quantity;
            }
        }

        $outlet_address   = "";
        if($purchaseOrder->outlets) {
            $company = $purchaseOrder->outlets->company;
            $district_name = ($purchaseOrder->outlets->districts) ? $purchaseOrder->outlets->districts->name : "";
            $area_name  = ($purchaseOrder->outlets->areas) ? $purchaseOrder->outlets->areas->name : "";

            $outlet_address = $purchaseOrder->outlets->road_no .", H: ".$purchaseOrder->outlets->plot_no.", ".$purchaseOrder->outlets->police_station.", ".$area_name.", ".$district_name;
        }elseif($purchaseOrder->warehouses) {
            $company = $purchaseOrder->warehouses->company;
        }else{
            $company = Company::where('id', 1)->first();
        }

//        return $purchaseOrder->outlets->districts;
        $return_data    = [
            'purchase_order'  =>  [
                'id'    => $purchaseOrder->id,
                'purchase_order_id'   => $purchaseOrder->purchase_order_id,
                'supplier_id'   => $purchaseOrder->supplier_id,
                'supplier_name' => $purchaseOrder->suppliers->name,
                'supplier_address' => $purchaseOrder->suppliers->address,
                'supplier_phone' => $purchaseOrder->suppliers->phone,
                'supplier_contact_person_name' => $purchaseOrder->suppliers->contact_person_name,
                'receive_type'   => $purchaseOrder->receive_type,
                'reference_no'   => $purchaseOrder->reference_no,
                'challan_no'   => $purchaseOrder->challan_no,
                'order_date'   => $purchaseOrder->order_date,
                'delivery_date'   => $purchaseOrder->delivery_date,
                'start_date'   => $purchaseOrder->start_date,
                'end_date'   => $purchaseOrder->end_date,
                'total_quantity'   => $purchaseOrder->total_qty,
                'total_value'   => ($purchaseOrder->total_value - $purchaseOrder->commission_value),
                'total_commission_value'   => $purchaseOrder->commission_value,
                'total_vat'   => $purchaseOrder->total_vat,
                'total_free_quantity'   => $total_free_quantity,
                'total_free_amount'   => $total_free_amount,
                'total_amount'   => $purchaseOrder->total_amount,
                'remarks'   => $purchaseOrder->remarks ?? 'N/A',
                'approve_status'   => $purchaseOrder->approve_status,
                'approve_status_name'   => purchaseOrderApproveStatusName($purchaseOrder->approve_status),
                'status'   => $purchaseOrder->status,
                'outlet_id' => $purchaseOrder->delivery_to_outlet,
                'outlets'   => $purchaseOrder->outlets,
                'outlet_address'   => $outlet_address,
                'warehouse_id'   => $purchaseOrder->warehouse_id,
                'warehouses'    => $purchaseOrder->warehouses,
                'company'       => $company,
                'user_id'   => $purchaseOrder->user_id,
                'created_at'   => $purchaseOrder->created_at,
                'updated_at'   => $purchaseOrder->updated_at,
            ],
            'purchase_order_product'  => $order_product_data
        ];

        return $this->sendResponse($return_data, 'Purchase Receive retrieved successfully');
    }
    /**
     * Update the specified PurchaseOrder in storage.
     * PUT/PATCH /purchaseOrders/{id}
     *
     * @param int $id
     * @param UpdatePurchaseOrderAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderAPIRequest $request)
    {
        $this->validate($request, [
            'supplier_id'   => 'required',
            'order_date'    => 'required',
            'reference_no'  => 'required',
            'supplier_payment_type'  => 'required',
        ]);

        // For Purchase Products
        $products = json_decode($request->products);

        $product_array = [];
        $total_quantity = 0;
        $total_product_value = 0;
        $total_free_amount = 0;
        $total_discount_amount = 0;
        $total_product_amount = 0;
        $total_vat_amount = 0;
        if(count($products) > 0) {

            foreach($products as $product) {

//                  if($product->checked) {
                if($product->product_id != "" && ($product->tp > 0 && $product->tp != '') && ($product->order_qty > 0 && $product->order_qty != '')) {

                    $product_value = ($product->tp * $product->order_qty);
                    $free_amount = ($product->tp * 0);
                    $discount_amount = ($product_value * 0) / 100;
                    $product_amount = $product_value - $discount_amount;

                    $product_array[]    = new OrderRequisitionDetail([
                        'product_id'    => $product->product_id,
                        'product_unit_id'    => $product->product_unit_id,
                        'order_purchase_price'    => $product->tp,
                        'order_quantity'   => $product->order_qty,
                        'order_discount_percent'  => 0,
                        'order_free_quantity'  => 0,
                        'order_product_value'  => $product_value,
                        'order_discount_amount'  => $discount_amount,
                        'order_free_amount'  => $free_amount,
                        'order_vat_amount'  => 0,
                        'order_amount'    => $product_amount,
//                        'order_line_notes'    => $product->line_notes,
                    ]);

                    $total_quantity += $product->order_qty;
                    $total_product_value += $product_value;
                    $total_discount_amount += $discount_amount;
                    $total_free_amount  += $free_amount;
                    $total_product_amount += $product_amount;
                    $total_vat_amount   += 0;
                }
            }
        }


        if (empty($product_array)) {
            return $this->sendError('Please give quantity getter then 0 at list one products item');
        }

        $inputs = [
            'reference_no'  => $request->get('reference_no'),
            'supplier_id'   => $request->get('supplier_id'),
            'supplier_payment_type' => $request->get('supplier_payment_type'),
            'number_of_po' => $request->get('number_of_po'),
            'supply_schedule' => $request->get('supply_schedule'),
            'order_date' => customDateFormat($request->get('order_date')),
            'delivery_date' => customDateFormat($request->get('delivery_date')),
            'warehouse_id' => $request->get('warehouse_id') ?? 0,
            'delivery_to_outlet' => $request->get('outlet_id') ?? 0,
//            'start_date' => $request->get('start_date'),
//            'end_date' => $request->get('end_date'),
            'total_qty' => $total_quantity,
            'total_value' => $total_product_value,
            'commission_value' => $total_discount_amount,
            'total_vat' => $total_vat_amount,
            'total_free_amount' => $total_free_amount,
            'total_amount' => $total_product_amount,
        ];

        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        DB::beginTransaction();
        try {
            $purchaseOrder = $this->purchaseOrderRepository->update($inputs, $id);

            $purchase_product_delete = $purchaseOrder->purchase_products()->delete();

            $purchase_product = $purchaseOrder->purchase_products()->saveMany($product_array);
            DB::commit();

            return $this->sendResponse($purchaseOrder->toArray(), 'Purchase Order update successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified PurchaseOrder from storage.
     * DELETE /purchaseOrders/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchaseOrder->delete();

        return $this->sendSuccess('Purchase Order deleted successfully');
    }


    // Generate PDF for PO Invoice
    public function generatePDF($id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $order_product_data = [];
        $total_free_quantity = 0;
        $total_free_amount   = 0;
        if(count($purchaseOrder->purchase_products) > 0)
        {
            foreach ($purchaseOrder->purchase_products as $order_details)
            {
                $order_product_data[] = (object)[
                    'receive_details_id'    => $order_details->id,
                    'product_id'    => $order_details->product_id,
                    'product_unit_id'   => $order_details->product_unit_id,
                    'product_name'  => $order_details->product->product_name,
                    'product_code'  => $order_details->product->product_code,
                    'cost_price'    => $order_details->order_purchase_price,
                    'depo_price'    => $order_details->product->depo_price,
                    'mrp_price'     => $order_details->product->mrp_price,
                    'purchase_measuring_unit'   => $order_details->product->purchase_measuring_unit,
                    'sales_measuring_unit'  => $order_details->product->sales_measuring_unit,
                    'purchase_unit' => $order_details->product->purchase_unit->unit_code ?? '',
                    'sales_unit'    => $order_details->product->sales_unit->sales_unit ?? '',
                    'carton_size'   => $order_details->product->carton_size ?? 'N/A',
                    'order_quantity'       => $order_details->order_quantity,
                    'order_discount_amount'       => $order_details->order_discount_amount,
                    'order_free_quantity'       => $order_details->order_free_quantity,
                    'order_free_amount'       => $order_details->order_free_amount,
                    'order_vat_amount'       => $order_details->order_vat_amount,
                    'order_amount'       => ($order_details->order_amount != 0) ? $order_details->order_amount : (($order_details->order_purchase_price * $order_details->order_quantity) - $order_details->order_discount_amount) + $order_details->order_vat_amount,

                ];

                $total_free_quantity += $order_details->order_free_quantity;
                $total_free_amount += $order_details->order_purchase_price *             $order_details->order_free_quantity;
            }
        }

        $outlet_address   = "";
        if($purchaseOrder->outlets) {
            $company = $purchaseOrder->outlets->company;
            $district_name = ($purchaseOrder->outlets->districts) ? $purchaseOrder->outlets->districts->name : "";
            $area_name  = ($purchaseOrder->outlets->areas) ? $purchaseOrder->outlets->areas->name : "";

            $outlet_address = $purchaseOrder->outlets->road_no .", H: ".$purchaseOrder->outlets->plot_no.", ".$purchaseOrder->outlets->police_station.", ".$area_name.", ".$district_name;
        }elseif($purchaseOrder->warehouses) {
            $company = $purchaseOrder->warehouses->company;
        }else{
            $company = Company::where('id', 1)->first();
        }

        $path = asset('uploads/company/logo.png');
//        $type = pathinfo($path, PATHINFO_EXTENSION);
//        $data = file_get_contents($path);
////        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
//
//        return response()->json($data);

        $return_data    = [
            'purchase_order'  =>  (object)[
                'id'    => $purchaseOrder->id,
                'purchase_order_id'   => $purchaseOrder->purchase_order_id,
                'supplier_id'   => $purchaseOrder->supplier_id,
                'supplier_name' => $purchaseOrder->suppliers->name,
                'supplier_address' => $purchaseOrder->suppliers->address,
                'supplier_phone' => $purchaseOrder->suppliers->phone,
                'supplier_contact_person_name' => $purchaseOrder->suppliers->contact_person_name,
                'receive_type'   => $purchaseOrder->receive_type,
                'reference_no'   => $purchaseOrder->reference_no,
                'challan_no'   => $purchaseOrder->challan_no,
                'order_date'   => $purchaseOrder->order_date,
                'delivery_date'   => $purchaseOrder->delivery_date,
                'start_date'   => $purchaseOrder->start_date,
                'end_date'   => $purchaseOrder->end_date,
                'total_quantity'   => $purchaseOrder->total_qty,
                'total_value'   => $purchaseOrder->total_value,
                'total_commission_value'   => $purchaseOrder->commission_value,
                'total_vat'   => $purchaseOrder->total_vat,
                'total_free_quantity'   => $total_free_quantity,
                'total_free_amount'   => $total_free_amount,
                'total_amount'   => $purchaseOrder->total_amount,
                'remarks'   => $purchaseOrder->remarks ?? 'N/A',
                'approve_status'   => $purchaseOrder->approve_status,
                'approve_status_name'   => purchaseOrderApproveStatusName($purchaseOrder->approve_status),
                'status'   => $purchaseOrder->status,
                'outlet_id' => $purchaseOrder->delivery_to_outlet,
                'outlets'   => $purchaseOrder->outlets,
                'outlet_address'    => $outlet_address,
                'warehouse_id'   => $purchaseOrder->warehouse_id,
                'warehouses'    => $purchaseOrder->warehouses,
                'company'       => $company,
                'company_logo'  => $path,
                'user_id'   => $purchaseOrder->user_id,
                'created_at'   => $purchaseOrder->created_at,
                'updated_at'   => $purchaseOrder->updated_at,
            ],
            'purchase_order_product'  => $order_product_data
        ];

//        return view('invoices.purchase_order_invoice', $return_data);
        $pdf_doc    = PDF::loadView('invoices.purchase_order_invoice_new', $return_data);

//        $pdf_doc->setPaper("a4", "landscape");
        $pdf_doc->setPaper("a4", "portrait");
        $output = $pdf_doc->output();

//        return $output;

        if (!is_dir(public_path('/upload_order_invoice'))) {
            // dir doesn't exist, make it
            mkdir(public_path('/upload_order_invoice'), 0777, true);
        }
        File::cleanDirectory(public_path('/upload_order_invoice'));

        $fileName = $purchaseOrder->reference_no.".pdf";
        $full_path = asset('public/upload_order_invoice/'.$fileName);

        if(file_put_contents(public_path('upload_order_invoice/'.$fileName), $output, FILE_USE_INCLUDE_PATH )) {
            $response = [
              'file_path'   => $full_path
            ];

            return $this->sendResponse($response, 'Invoice Retrieve Successfully');
        }
        else{
            return $this->sendError('Something Went Wrong, Please try again');
        }

    }

    // Approve Purchase Order
    public function approvePurchaseOrder(Request $request, $id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchaseOrder->update(['approve_status' => 1]);

        return $this->sendSuccess('Purchase Order Approved Successfully');
    }
    // Reject Purchase Order
    public function rejectPurchaseOrder(Request $request, $id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchaseOrder->update(['approve_status' => 2, 'remarks' => $request->get('remarks')]);

        return $this->sendSuccess('Purchase Order Rejected Successfully');
    }

    // PO SEND CONFIRM
    public function sendPOForVendor(Request $request, $id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchaseOrder->update(['send_status' => 1]);

        return $this->sendSuccess('Confirm PO Send Successfully');
    }

    // Get Product Data
    public function getProductData(Request $request)
    {
        $products = Product::where('status', 1)->get();
        $product_data = [];
        if(count($products) > 0){
            foreach($products as $product) {
                $product_data[] = [
                    'id' =>  $product->id,
                    'name' => $product->product_name,
                    'code' => $product->product_code,
                    'unit_code' => ($product->purchase_unit) ? $product->purchase_unit->unit_code : '',
                    'product_unit_id'   => $product->purchase_measuring_unit,
                    'carton_size' => $product->carton_size ?? '',
                    'purchase_price' => $product->cost_price,
                    'mrp' => $product->mrp_price,
                    'qty' => 0,
                    'disc_percent' => 0,
                    'free_qty' => 0,
                    'value' => 0.000,
                    'disc_amount' => 0,
                    'free_amount' => 0,
                    'vat' => 0.000,
                    'amount' => 0.00,
                    'supplier_id' => '',
//                    'checked' =>  false,
                    'checked' =>  true,
                    'required_supplier' => false,
                ];
            }
        }

        $data = [
            'product_data'  => $product_data
        ];

        return $this->sendResponse($data, 'Get product data retrieved successfully');
    }

    // Get Supplier Data
    public function getSupplierData(Request $request)
    {
//        return response()->json($request->supplier_id);

        $supplier = Supplier::find($request->supplier_id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        $products = Product::where('status', 1)->get();
        $product_data = [];
        if(count($products) > 0){
            foreach($products as $product) {
                $product_data[] = [
                    'id' =>  $product->id,
                    'name' => $product->product_name,
                    'code' => $product->product_code,
                    'unit_code' => $product->purchase_unit->unit_code,
                    'product_unit_id'   => $product->purchase_measuring_unit,
                    'carton_size' => $product->carton_size ?? '',
                    'purchase_price' => $product->cost_price,
                    'mrp' => $product->mrp_price,
                    'qty' => 0,
                    'disc_percent' => 0,
                    'free_qty' => 0,
                    'value' => 0.000,
                    'disc_amount' => 0,
                    'free_amount' => 0,
                    'vat' => 0.000,
                    'amount' => 0.00,
                    'supplier_id' => '',
                    'checked' =>  false,
                ];
            }
        }

        $data = [
            'supplier'  => $supplier,
            'product_data'  => $product_data
        ];

        return $this->sendResponse($data, 'Supplier product data retrieved successfully');
    }


    // Requisition Purchase Order Edit
    public function requisitionPurchaseOrderEdit($id)
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if(empty($purchaseOrder)) {
            return $this->sendError('Purchase Order Not Found');
        }

        $requisition_product_data = [];
        $total_approve_quantity = 0;
        $total_approve_amount   = 0;
        if(count($purchaseOrder->purchase_products) > 0)
        {
            foreach ($purchaseOrder->purchase_products as $requisition_product)
            {
                $requisition_product_data[] = [
                    'order_details_id'    => $requisition_product->id,
                    'id'    => $requisition_product->product_id,
                    'product_unit_id'   => $requisition_product->product_unit_id,
                    'product_name'  => $requisition_product->product->product_name,
                    'product_code'  => $requisition_product->product->product_code,
                    'cost_price'    => $requisition_product->order_purchase_price,
                    'depo_price'    => $requisition_product->product->depo_price,
                    'mrp_price'     => $requisition_product->product->mrp_price,
                    'purchase_measuring_unit'   => $requisition_product->product->purchase_measuring_unit,
                    'sales_measuring_unit'  => $requisition_product->product->sales_measuring_unit,
                    'purchase_unit' => $requisition_product->product->purchase_unit->unit_code ?? '',
                    'sales_unit'    => $requisition_product->product->sales_unit->sales_unit ?? '',
                    'carton_size'   => $requisition_product->product->carton_size ?? 'N/A',
                    'req_carton_qty' => 0,
                    'req_qty'       => $requisition_product->requisition_quantity,
                    'approve_qty'   => $requisition_product->approve_quantity,
                    'order_qty'     => $requisition_product->order_quantity != 0 ? $requisition_product->order_quantity : $requisition_product->approve_quantity,
                    'disc_percent'  => $requisition_product->order_discount_percent,
                    'free_qty'      => $requisition_product->order_free_quantity,
                    'vat'           => $requisition_product->order_vat_amount,
                    'line_notes'    => $requisition_product->order_line_notes,
                    'supplier_id'   => '',
                    'checked' => true,

                ];

                $total_approve_quantity += $requisition_product->approve_quantity;
                $total_approve_amount += $requisition_product->order_purchase_price *             $requisition_product->approve_quantity;
            }
        }

        $return_data    = [
            'purchase_order'  =>  new PurchaseOrderResource($purchaseOrder),
            'purchase_order_products'  => $requisition_product_data
        ];

        return $this->sendResponse($return_data, 'Purchase Order retrieved successfully');
    }

    // Requisition Purchase Order Update
    public function confirmRequisitionPurchaseOrderEdit(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id'    => 'required',
            'order_date'    => 'required',
//            'delivery_date' => 'required',
//            'outlet_id'     => 'required',
            'reference_no'  => 'required',
//            'start_date'    => 'required',
//            'end_date'      => 'required',
        ]);

        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if(empty($purchaseOrder)) {
            return $this->sendError('Purchase Order Not Found');
        }

        // Product Array
        $products   = json_decode($request->products);
        if(empty($products)) {
            return $this->sendError('Product not available for purchase order');
        }

        DB::beginTransaction();

        try{

            $total_quantity = 0;
            $total_val_amount = 0;
            $total_discount_amount = 0;
            $total_free_amount = 0;
            $total_vat_amount = 0;
            $total_amount = 0;

            foreach($products as $requisition_details) {

                $discount_amount = ((($requisition_details->order_qty * $requisition_details->cost_price) * $requisition_details->disc_percent) / 100);
                $free_amount = ($requisition_details->cost_price * $requisition_details->free_qty);
                $value_amount = ($requisition_details->cost_price * $requisition_details->order_qty);
                $pt_amount = ($value_amount - $discount_amount) + $requisition_details->vat;


                $total_quantity += $requisition_details->order_qty;
                $total_val_amount   += $value_amount;
                $total_discount_amount += $discount_amount;
                $total_free_amount += $free_amount;
                $total_vat_amount += $requisition_details->vat;
                $total_amount += $pt_amount;

                $update_input = [
                    'order_quantity'    => $requisition_details->order_qty,
                    'order_purchase_price'    => $requisition_details->cost_price,
                    'order_discount_percent'  => $requisition_details->disc_percent,
                    'order_free_quantity'     => $requisition_details->free_qty,
                    'order_discount_amount'   => $discount_amount,
                    'order_free_amount'       => $free_amount,
                    'order_vat_amount'        => $requisition_details->vat,
                    'order_line_notes'        => $requisition_details->line_notes,
                ];

                $porder_data    = OrderRequisitionDetail::find($requisition_details->order_details_id);
                if($porder_data) {
                    $porder_data->update($update_input);
                }

            }

            $order_inputs = [
                'reference_no'  => $request->get('reference_no'),
                'supplier_id'   => $request->get('supplier_id'),
//                        'supplier_payment_type' => $request->get('supplier_payment_type'),
//                        'number_of_po' => $request->get('number_of_po'),
//                        'supply_schedule' => $request->get('supply_schedule'),
                'order_date' => customDateFormat($request->get('order_date')),
                'delivery_date' => customDateFormat($request->get('delivery_date')),
                'delivery_to_outlet' => $request->get('outlet_id') ?? 0,
                'warehouse_id' => $request->get('warehouse_id') ?? 0,
//                'start_date' => $request->get('start_date'),
//                'end_date' => $request->get('end_date'),
                'total_qty' => $total_quantity,
                'total_value' => $total_val_amount,
                'commission_value' => $total_discount_amount,
                'total_vat' => $total_vat_amount,
                'total_free_amount' => $total_free_amount,
                'total_amount' => $total_amount,
            ];

            $purchaseOrderUpdate = $purchaseOrder->update($order_inputs);

            DB::commit();
            return $this->sendSuccess('Purchase Order Update Successfully!');

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    //Get Reference No
    public function getOrderReferenceNo()
    {
        $reference_no = $this->returnPurchaseOrderNo("PO");

        return $this->sendResponse(['reference_no' => $reference_no], 'Get Reference no successfully');
    }


    // Submit Before Preview for single PO
    public function getBeforeSubmitPreviewSPO(Request $request)
    {
        $supplier_id    = $request->get('supplier_id');
        $warehouse_id   = $request->get('warehouse_id');
        $outlet_id  = $request->get('outlet_id');

        $supplier = "";
        if($supplier_id != "") {
            $supplier = Supplier::where('id', $supplier_id)->first();
        }
        $warehouse = "";
        if($warehouse_id != "") {
          $warehouse    = Warehouse::with('company')->where('id', $warehouse_id)->first();
        }
        $outlet = "";
        $outlet_address   = "";
        if($outlet_id != "") {
            $outlet    = Outlet::with('company', 'districts', 'areas')->where('id', $outlet_id)->first();

            $district_name = ($outlet->districts) ? $outlet->districts->name : "";
            $area_name  = ($outlet->areas) ? $outlet->areas->name : "";

            $outlet_address = $outlet->road_no .", H: ".$outlet->plot_no.", ".$outlet->police_station.", ".$area_name.", ".$district_name;
        }

        if($outlet != "" && $outlet->company) {
            $company = $outlet->company;
        }elseif($warehouse != "" && $warehouse->company) {
            $company = $warehouse->company;
        }else{
            $company = "";
        }

        $response = [
            'supplier_code' => '',
            'supplier_name' => $supplier->name,
            'supplier_address'  => $supplier->address,
            'supplier_contact_person_name'  => $supplier->contact_person_name,
            'supplier_phone'    => $supplier->phone,
            'outlet'   => $outlet,
            'outlet_address' => $outlet_address,
            'warehouse' => $warehouse,
            'company'   => $company
        ];

        return $this->sendResponse($response, 'Data retrieve successfully Done');
    }

    // Submit Before Preview for Multiple PO
    public function getBeforeSubmitPreviewMPO(Request $request)
    {

        $this->validate($request, [
//                'supplier_id'   => 'required',
            'order_date'    => 'required',
//            'delivery_date' => 'required',
            'reference_no'  => 'required',
//            'start_date'    => 'required',
//            'end_date'      => 'required',
        ]);

        // For Purchase Products
        $products = json_decode($request->get('products'));
        $warehouse_id = $request->get('warehouse_id');
        $outlet_id = $request->get('outlet_id');

        $warehouse = "";
        if($warehouse_id != "") {
            $warehouse    = Warehouse::with('company')->where('id', $warehouse_id)->first();
        }

        $outlet = "";
        $outlet_address   = "";
        if($outlet_id != "") {
            $outlet    = Outlet::with('company', 'districts', 'areas')->where('id', $outlet_id)->first();

            $district_name = ($outlet->districts) ? $outlet->districts->name : "";
            $area_name  = ($outlet->areas) ? $outlet->areas->name : "";

            $outlet_address = $outlet->road_no .", H: ".$outlet->plot_no.", ".$outlet->police_station.", ".$area_name.", ".$district_name;
        }

        if($outlet != "" && $outlet->company) {
            $company = $outlet->company;
        }elseif($warehouse != "" && $warehouse->company) {
            $company = $warehouse->company;
        }else{
            $company = "";
        }

        $supplier_array = [];
        $supplier_product_array = [];
        if(count($products) > 0) {
            foreach ($products as $product) {
                if(($product->product_id != '' && $product->product_id != 0) && ($product->tp > 0 && $product->tp != '') && ($product->order_qty > 0 && $product->order_qty != '') && ($product->supplier_id != '' && $product->supplier_id != 0)) {

                    if(!in_array($product->supplier_id, $supplier_array)) {
                        $supplier_array[]   = $product->supplier_id;
                    }
                    $supplier_product_array[$product->supplier_id][] = $product;

                }
            }
        }

        if(empty($supplier_product_array)) {
            return $this->sendError('Product not available for purchase order');
        }

        $purchase_orders = [];
        $current_reference_no = $this->returnPurchaseOrderNo("PO");
        $separator = explode("-", $current_reference_no);
        if(count($supplier_array) > 0) {
            for($s=0; $s<count($supplier_array); $s++) {
                $total_quantity = 0;
                $total_product_value = 0;
                $total_free_amount = 0;
                $total_discount_amount = 0;
                $total_product_amount = 0;
                $total_vat_amount = 0;

                foreach ($supplier_product_array[$supplier_array[$s]] as $supplier_product) {
                    $product_value = ($supplier_product->tp * $supplier_product->order_qty);
//                    $free_amount = ($supplier_product->tp * $supplier_product->free_qty);
                    $free_amount = ($supplier_product->tp * 0);
//                    $discount_amount = ($product_value * $supplier_product->disc_percent) / 100;
                    $discount_amount = ($product_value * 0) / 100;
                    $product_amount = $product_value - $discount_amount;

                    $total_quantity += $supplier_product->order_qty;
                    $total_product_value += $product_value;
                    $total_discount_amount += $discount_amount;
                    $total_free_amount  += $free_amount;
                    $total_product_amount += $product_amount;
                    $total_vat_amount   += 0;
                }

                $reference_no   = $separator[0]."-".str_pad(($separator[1] + $s), 5, '0', STR_PAD_LEFT);
                $supplier   = Supplier::where('id', $supplier_array[$s])->first();

                $purchase_orders[] = [
                    'reference_no'  => $reference_no,
                    'supplier_id'   => $supplier_array[$s],
                    'supplier_name' => $supplier->name,
                    'supplier_address'  => $supplier->address,
                    'supplier_contact_person_name'  => $supplier->contact_person_name,
                    'supplier_phone'    => $supplier->phone,
                    'supplier_payment_type' => 'cash_purchase',
                    'order_date' => customDateFormat($request->get('order_date')),
                    'delivery_date' => customDateFormat($request->get('delivery_date')),
                    'warehouse_id' => $warehouse_id ?? 0,
                    'delivery_to_outlet' => $outlet_id ?? 0,
                    'warehouse' => $warehouse,
                    'outlet'    => $outlet,
                    'outlet_address'    => $outlet_address,
                    'company'   => $company,
                    'total_qty' => $total_quantity,
                    'total_value' => $total_product_value,
                    'commission_value' => $total_discount_amount,
                    'total_vat' => $total_vat_amount,
                    'total_free_amount' => $total_free_amount,
                    'total_amount' => $total_product_amount,
                    'order_products'    => $supplier_product_array[$supplier_array[$s]]

                ];

            }

        }else{
            return $this->sendError('Please Select Supplier for all order product');
        }

        return $this->sendResponse($purchase_orders, 'Data retrieve successfully Done');
    }


}
