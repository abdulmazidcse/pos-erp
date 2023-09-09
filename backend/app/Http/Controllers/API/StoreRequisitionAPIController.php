<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStoreRequisitionAPIRequest;
use App\Http\Requests\API\UpdateStoreRequisitionAPIRequest;
use App\Http\Resources\StoreRequisitionResource;
use App\Models\OrderRequisitionDetail;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StoreRequisition;
use App\Repositories\StoreRequisitionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class StoreRequisitionController
 * @package App\Http\Controllers\API
 */

class StoreRequisitionAPIController extends AppBaseController
{
    /** @var  StoreRequisitionRepository */
    private $storeRequisitionRepository;

    public function __construct(StoreRequisitionRepository $storeRequisitionRepo)
    {
        $this->storeRequisitionRepository = $storeRequisitionRepo;
    }

    /**
     * Display a listing of the StoreRequisition.
     * GET|HEAD /storeRequisitions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //        $storeRequisitions = $this->storeRequisitionRepository->all(
        //            $request->except(['skip', 'limit']),
        //            $request->get('skip'),
        //            $request->get('limit')
        //        );

        $storeRequisitions = StoreRequisition::orderBy('id', 'desc')->get();

        $return_data    = StoreRequisitionResource::collection($storeRequisitions);

        return $this->sendResponse($return_data, 'Store Requisitions retrieved successfully');
    }


    public function getRequisitionData()
    {
        $requisition_data   = StoreRequisition::where('approve_status', 0)->orderBy('id', 'desc')->get();

        $return_data    = StoreRequisitionResource::collection($requisition_data);

        return $this->sendResponse($return_data, 'Store Requisitions retrieved successfully');
    }

    /**
     * Store a newly created StoreRequisition in storage.
     * POST /storeRequisitions
     *
     * @param CreateStoreRequisitionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateStoreRequisitionAPIRequest $request)
    {
        $this->validate($request, [
            'requisition_date'  => 'required',
            'requisition_no'    => 'required|unique:store_requisitions,requisition_no'
        ]);

        // For Purchase Products
        $products = json_decode($request->products);

        $product_array = [];
        $total_quantity = 0;
        $total_product_value = 0;
        $total_product_amount = 0;
        if (count($products) > 0) {

            foreach ($products as $product) {

                $product_value = ($product->cost_price * $product->req_qty);
                $product_amount = $product_value;

                $product_array[]    = new OrderRequisitionDetail([
                    'product_id'    => $product->id,
                    'product_unit_id'    => $product->purchase_measuring_unit,
                    'requisition_purchase_price'    => $product->cost_price,
                    'requisition_quantity'   => $product->req_qty,
                    'requisition_product_value'  => $product_value,
                    'requisition_amount'        => $product_amount,
                ]);

                $total_quantity += $product->req_qty;
                $total_product_value += $product_value;
                $total_product_amount += $product_amount;
            }
        }

        if (empty($product_array)) {
            return $this->sendError('Please at list one products added for requisition');
        }

        $inputs  = [
            'outlet_id' => 1,
            'requisition_date'  => $request->get('requisition_date'),
            'requisition_no'    => $request->get('requisition_no'),
            'total_quantity'    => $total_quantity,
            'total_value'       => $total_product_value,
            'total_amount'      => $total_product_amount,
        ];

        DB::beginTransaction();
        try {
            $storeRequisition = $this->storeRequisitionRepository->create($inputs);
            $store_requisition_product = $storeRequisition->purchase_requisition()->saveMany($product_array);
            DB::commit();

            return $this->sendResponse($storeRequisition->toArray(), 'Store Requisition submit successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified StoreRequisition.
     * GET|HEAD /storeRequisitions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);

        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition Not Found');
        }

        $requisition_product_data = [];
        $total_approve_quantity = 0;
        $total_approve_amount   = 0;
        if (count($storeRequisition->purchase_requisition) > 0) {
            foreach ($storeRequisition->purchase_requisition as $requisition_product) {
                $requisition_product_data[] = [
                    'requisition_details_id'    => $requisition_product->id,
                    'id'    => $requisition_product->product_id,
                    'product_unit_id'   => $requisition_product->product_unit_id,
                    'product_name'  => $requisition_product->product->product_name,
                    'product_code'  => $requisition_product->product->product_code,
                    'cost_price'    => $requisition_product->requisition_purchase_price,
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

                ];

                $total_approve_quantity += $requisition_product->approve_quantity;
                $total_approve_amount += $requisition_product->requisition_purchase_price *             $requisition_product->approve_quantity;
            }
        }

        $return_data    = [
            'requisition_data'  =>  [
                'id'    => $storeRequisition->id,
                'requisition_no'   => $storeRequisition->requisition_no,
                'requisition_date'   => $storeRequisition->requisition_date,
                'outlet_id'   => $storeRequisition->outlet_id,
                'outlet_name'   => $storeRequisition->outlets->name ?? '',
                'total_quantity'   => $storeRequisition->total_quantity,
                'total_value'   => $storeRequisition->total_value,
                'total_amount'   => $storeRequisition->total_amount,
                'approve_status'   => $storeRequisition->approve_status,
                'approve_status_name'   => $this->approveStatusName($storeRequisition->approve_status),
                'remarks'   => $storeRequisition->remarks ?? 'N/A',
                'status'   => $storeRequisition->status,
                'warehouse_id'   => $storeRequisition->warehouse_id,
                'user_id'   => $storeRequisition->user_id,
                'total_approve_quantity'   => $total_approve_quantity,
                'total_approve_amount'   => $total_approve_amount,
                'created_at'   => $storeRequisition->created_at,
                'updated_at'   => $storeRequisition->updated_at,
            ],
            'requisition_products'  => $requisition_product_data
        ];

        return $this->sendResponse($return_data, 'Store Requisition retrieved successfully');
    }


    // Edit Data
    public function edit($id)
    {
        $storeRequisition = $this->storeRequisitionRepository->find($id);

        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition Not Found');
        }

        $requisition_product_data = [];
        if (count($storeRequisition->purchase_requisition) > 0) {
            foreach ($storeRequisition->purchase_requisition as $requisition_product) {
                $requisition_product_data[] = [
                    'requisition_details_id'    => $requisition_product->id,
                    'id'    => $requisition_product->product_id,
                    'product_unit_id'   => $requisition_product->product_unit_id,
                    'product_name'  => $requisition_product->product->product_name,
                    'product_code'  => $requisition_product->product->product_code,
                    'cost_price'    => $requisition_product->requisition_purchase_price,
                    'depo_price'    => $requisition_product->product->depo_price,
                    'mrp_price'     => $requisition_product->product->mrp_price,
                    'purchase_measuring_unit'   => $requisition_product->product->purchase_measuring_unit,
                    'sales_measuring_unit'  => $requisition_product->product->sales_measuring_unit,
                    'purchase_unit' => $requisition_product->product->purchase_unit->unit_code ?? '',
                    'sales_unit'    => $requisition_product->product->sales_unit->sales_unit ?? '',
                    'carton_size'   => $requisition_product->product->carton_size ?? 'N/A',
                    'req_carton_qty' => 0,
                    'req_qty'       => $requisition_product->requisition_quantity,
                ];
            }
        }

        $return_data    = [
            'requisition_data'  =>  new StoreRequisitionResource($storeRequisition),
            'requisition_products'  => $requisition_product_data
        ];


        return $this->sendResponse($return_data, 'Data Retrieved Successfully!');
    }

    /**
     * Update the specified StoreRequisition in storage.
     * PUT/PATCH /storeRequisitions/{id}
     *
     * @param int $id
     * @param UpdateStoreRequisitionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStoreRequisitionAPIRequest $request)
    {
        $this->validate($request, [
            'requisition_date'  => 'required',
            'requisition_no'    => 'required|unique:store_requisitions,requisition_no,' . $id
        ]);


        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);
        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition not found');
        }
        $requisition_details = $storeRequisition->purchase_requisition->pluck("id")->toArray();

        // For Requisition Products
        $products = json_decode($request->products);

        if (empty($products)) {
            return $this->sendError('Please at list one products added for requisition');
        }

        DB::beginTransaction();
        try {

            $new_product_array = [];
            $total_quantity = 0;
            $total_product_value = 0;
            $total_product_amount = 0;
            $delete_array = [];
            foreach ($products as $product) {
                $product_value = ($product->cost_price * $product->req_qty);
                $product_amount = $product_value;
                if (isset($product->requisition_details_id)) {
                    if (($key = array_search($product->requisition_details_id, $requisition_details)) !== false) {
                        unset($requisition_details[$key]);
                        $delete_array = array_values($requisition_details);
                    }
                    $update_input = [
                        'requisition_purchase_price'    => $product->cost_price,
                        'requisition_quantity'   => $product->req_qty,
                        'requisition_product_value'  => $product_value,
                        'requisition_amount'        => $product_amount,
                    ];
                    $purchase_requisition_update = OrderRequisitionDetail::find($product->requisition_details_id);
                    if ($purchase_requisition_update) {
                        $purchase_requisition_update->update($update_input);
                    }
                } else {
                    $delete_array = $requisition_details;
                    $new_product_array[]    = new OrderRequisitionDetail([
                        'product_id'    => $product->id,
                        'product_unit_id'    => $product->purchase_measuring_unit,
                        'requisition_purchase_price'    => $product->cost_price,
                        'requisition_quantity'   => $product->req_qty,
                        'requisition_product_value'  => $product_value,
                        'requisition_amount'        => $product_amount,
                    ]);
                }

                $total_quantity += $product->req_qty;
                $total_product_value += $product_value;
                $total_product_amount += $product_amount;
            }

            $inputs  = [
                'outlet_id' => 1,
                'requisition_date'  => $request->get('requisition_date'),
                'requisition_no'    => $request->get('requisition_no'),
                'total_quantity'    => $total_quantity,
                'total_value'       => $total_product_value,
                'total_amount'      => $total_product_amount,
            ];

            if (count($delete_array) > 0) {
                $delete_requisition_details = OrderRequisitionDetail::whereIn('id', $delete_array)->forceDelete();
            }
            $storeRequisition = $this->storeRequisitionRepository->update($inputs, $id);

            if (count($new_product_array) > 0) {
                $store_requisition_product = $storeRequisition->purchase_requisition()->saveMany($new_product_array);
            }
            DB::commit();

            return $this->sendResponse($storeRequisition->toArray(), 'Store Requisition update successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified StoreRequisition from storage.
     * DELETE /storeRequisitions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);

        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition not found');
        }

        $storeRequisition->delete();

        return $this->sendSuccess('Store Requisition deleted successfully');
    }


    public function getProduct(Request $request)
    {
        $product = Product::with(['purchase_unit', 'sales_unit'])->where('product_code', $request->code)->first();

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $return_data    = [
            'id'    => $product->id,
            'product_name'  => $product->product_name,
            'product_code'  => $product->product_code,
            'cost_price'    => $product->cost_price,
            'depo_price'    => $product->depo_price,
            'mrp_price'     => $product->mrp_price,
            'purchase_measuring_unit'   => $product->purchase_measuring_unit,
            'sales_measuring_unit'  => $product->sales_measuring_unit,
            'purchase_unit' => $product->purchase_unit->unit_code ?? '',
            'sales_unit'    => $product->sales_unit->sales_unit ?? '',
            'carton_size'   => $product->carton_size ?? 'N/A',
            'req_carton_qty' => 0,
            'req_qty'       => 0,

        ];

        return $this->sendResponse($return_data, 'Product retrieved successfully');
    }


    // For Approval
    public function getRequisitionProductById($id)
    {

        $storeRequisition = $this->storeRequisitionRepository->find($id);

        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition Not Found');
        }

        $requisition_product_data = [];
        if (count($storeRequisition->purchase_requisition) > 0) {
            foreach ($storeRequisition->purchase_requisition as $requisition_product) {
                $requisition_product_data[] = [
                    'requisition_details_id'    => $requisition_product->id,
                    'id'    => $requisition_product->product_id,
                    'product_unit_id'   => $requisition_product->product_unit_id,
                    'product_name'  => $requisition_product->product->product_name,
                    'product_code'  => $requisition_product->product->product_code,
                    'cost_price'    => $requisition_product->requisition_purchase_price,
                    'depo_price'    => $requisition_product->product->depo_price,
                    'mrp_price'     => $requisition_product->product->mrp_price,
                    'purchase_measuring_unit'   => $requisition_product->product->purchase_measuring_unit,
                    'sales_measuring_unit'  => $requisition_product->product->sales_measuring_unit,
                    'purchase_unit' => $requisition_product->product->purchase_unit->unit_code ?? '',
                    'sales_unit'    => $requisition_product->product->sales_unit->sales_unit ?? '',
                    'carton_size'   => $requisition_product->product->carton_size ?? 'N/A',
                    'req_carton_qty' => 0,
                    'req_qty'       => $requisition_product->requisition_quantity,
                    'approve_qty'   => ($requisition_product->approve_quantity != 0) ? $requisition_product->approve_quantity : $requisition_product->requisition_quantity,
                ];
            }
        }

        $return_data    = [
            'requisition_products'  => $requisition_product_data
        ];

        return $this->sendResponse($return_data, 'Data Retrieved Successfully!');
    }

    // Approve Requisition
    public function approveStoreRequisition($id, Request $request)
    {
        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);
        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition not found');
        }
        // For Requisition Products
        $products = json_decode($request->products);

        if (empty($products)) {
            return $this->sendError('Please at list one products added for requisition');
        }

        DB::beginTransaction();
        try {

            foreach ($products as $product) {
                $update_input = [
                    'approve_quantity'    => $product->approve_qty,
                ];
                $purchase_requisition_update = OrderRequisitionDetail::find($product->requisition_details_id);
                if ($purchase_requisition_update) {
                    $purchase_requisition_update->update($update_input);
                }
            }

            $inputs  = [
                'approve_status' => 1,
                'remarks'  => $request->get('remarks')
            ];

            $storeRequisition = $this->storeRequisitionRepository->update($inputs, $id);

            DB::commit();

            return $this->sendResponse($storeRequisition->toArray(), 'Store Requisition Approved update successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }


    public function rejectStoreRequisition($id, Request $request)
    {
        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);
        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition not found');
        }

        DB::beginTransaction();
        try {

            $inputs  = [
                'approve_status' => 2,
                'remarks'  => $request->get('remarks')
            ];

            $storeRequisition = $this->storeRequisitionRepository->update($inputs, $id);

            DB::commit();

            return $this->sendResponse($storeRequisition->toArray(), 'Store Requisition Rejected Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }


    // Store Requisition Purchase Order
    public function storeRequisitionPurchaseOrder($id)
    {
        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);

        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition Not Found');
        }

        $requisition_product_data = [];
        $total_approve_quantity = 0;
        $total_approve_amount   = 0;
        if (count($storeRequisition->purchase_requisition) > 0) {
            $requisition_products = $storeRequisition->purchase_requisition()->where('order_status', '!=', 2)->get();
            foreach ($requisition_products as $requisition_product) {
                $requisition_product_data[] = [
                    'requisition_details_id'    => $requisition_product->id,
                    'id'    => $requisition_product->product_id,
                    'product_unit_id'   => $requisition_product->product_unit_id,
                    'product_name'  => $requisition_product->product->product_name,
                    'product_code'  => $requisition_product->product->product_code,
                    'cost_price'    => $requisition_product->requisition_purchase_price,
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
                    'prev_order_qty'   => $requisition_product->order_quantity,
                    'order_qty'     => $requisition_product->order_quantity != 0 ? ($requisition_product->approve_quantity - $requisition_product->order_quantity) : $requisition_product->approve_quantity,
                    'disc_percent'  => $requisition_product->order_discount_percent,
                    'free_qty'      => $requisition_product->order_free_quantity,
                    'vat'           => $requisition_product->requisition_vat_amount,
                    'line_notes'    => $requisition_product->requisition_line_notes,
                    'supplier_id'   => '',
                    'checked'       => false,

                ];

                $total_approve_quantity += $requisition_product->approve_quantity;
                $total_approve_amount += $requisition_product->requisition_purchase_price *             $requisition_product->approve_quantity;
            }
        }

        $return_data    = [
            'requisition_data'  =>  [
                'id'    => $storeRequisition->id,
                'requisition_no'   => $storeRequisition->requisition_no,
                'requisition_date'   => $storeRequisition->requisition_date,
                'outlet_id'   => ($storeRequisition->outlet_id != 0) ? $storeRequisition->outlet_id : '',
                'outlet_name'   => $storeRequisition->outlets->name ?? '',
                'total_quantity'   => $storeRequisition->total_quantity,
                'total_value'   => $storeRequisition->total_value,
                'total_amount'   => $storeRequisition->total_amount,
                'approve_status'   => $storeRequisition->approve_status,
                'approve_status_name'   => $this->approveStatusName($storeRequisition->approve_status),
                'remarks'   => $storeRequisition->remarks ?? 'N/A',
                'status'   => $storeRequisition->status,
                'warehouse_id'   => ($storeRequisition->warehouse_id != 0) ? $storeRequisition->warehouse_id : '',
                'user_id'   => $storeRequisition->user_id,
                'total_approve_quantity'   => $total_approve_quantity,
                'total_approve_amount'   => $total_approve_amount,
                'created_at'   => $storeRequisition->created_at,
                'updated_at'   => $storeRequisition->updated_at,

                'order_date'    => date("m/d/Y"),
                'delivery_date' => date("m/d/Y"),
                'reference_no'  => '',
                'start_date'    => '',
                'end_date'      => '',
                'commission_value'  => 0,
                'total_vat' => 0,
                'total_free_amount' => 0

            ],
            'requisition_products'  => $requisition_product_data,
            'reference_no'  => $this->returnPurchaseOrderNo("RPO"),
        ];

        return $this->sendResponse($return_data, 'Store Requisition retrieved successfully');
    }


    public function storeRequisitionPurchaseOrderConfirm(Request $request, $id)
    {
        $this->validate($request, [
            'order_date'    => 'required',
            //            'delivery_date' => 'required',
            //            'outlet_id'     => 'required',
            'reference_no'  => 'required',
            //            'start_date'    => 'required',
            //            'end_date'      => 'required',
        ]);

        /** @var StoreRequisition $storeRequisition */
        $storeRequisition = $this->storeRequisitionRepository->find($id);

        if (empty($storeRequisition)) {
            return $this->sendError('Store Requisition Not Found');
        }

        // Product Array
        $products   = json_decode($request->products);

        $supplier_array = [];
        $item_supplier_array = [];
        $supplier_product_array = [];
        $order_product_array    = [];
        foreach ($products as $product) {
            if ($product->checked) {
                if (!in_array($product->supplier_id, $supplier_array) && $product->supplier_id != "" && $product->supplier_id != 0) {
                    $supplier_array[]   = $product->supplier_id;
                }
                $supplier_product_array[$product->supplier_id][] = $product;
                $order_product_array[] = $product;
                if ($product->supplier_id != "" && $product->supplier_id != 0) {
                    $item_supplier_array[] = $product->supplier_id;
                }
            }
        }

        if (empty($order_product_array)) {
            return $this->sendError('Product not available for purchase order');
        }


        if (count($supplier_array) > 0 && (count($order_product_array) == count($item_supplier_array))) {
            DB::beginTransaction();
            try {
                for ($i = 0; $i < count($supplier_array); $i++) {
                    $order_details_ids = [];
                    $total_quantity = 0;
                    $total_val_amount = 0;
                    $total_discount_amount = 0;
                    $total_free_amount = 0;
                    $total_vat_amount = 0;
                    $total_amount = 0;

                    foreach ($supplier_product_array[$supplier_array[$i]] as $requisition_details) {

                        $order_details_ids[] = $requisition_details->requisition_details_id;

                        $discount_amount = ((($requisition_details->order_qty * $requisition_details->cost_price) * $requisition_details->disc_percent) / 100);
                        $free_amount = ($requisition_details->cost_price * $requisition_details->free_qty);
                        $value_amount = ($requisition_details->cost_price * $requisition_details->order_qty);
                        $pt_amount = ($value_amount - $discount_amount) + $requisition_details->vat;


                        $total_quantity += $requisition_details->order_qty;
                        $total_val_amount += $value_amount;
                        $total_discount_amount += $discount_amount;
                        $total_free_amount += $free_amount;
                        $total_vat_amount += $requisition_details->vat;
                        $total_amount += $pt_amount;

                        $update_input = [
                            'order_quantity' => $requisition_details->order_qty,
                            'order_purchase_price' => $requisition_details->cost_price,
                            'order_discount_percent' => $requisition_details->disc_percent,
                            'order_free_quantity' => $requisition_details->free_qty,
                            'order_discount_amount' => $discount_amount,
                            'order_free_amount' => $free_amount,
                            'order_vat_amount' => $requisition_details->vat,
                            'order_line_notes' => $requisition_details->line_notes,
                            'order_status' => 2

                        ];

                        $porder_data = OrderRequisitionDetail::find($requisition_details->requisition_details_id);
                        if ($porder_data) {
                            $porder_data->update($update_input);
                        }
                    }

                    $order_inputs = [
//                        'reference_no' => $request->get('reference_no'),
                        'reference_no' => $this->returnPurchaseOrderNo("RPO"),
                        'store_requisition_id' => $id,
                        'supplier_id' => $supplier_array[$i],
                        //                        'supplier_payment_type' => $request->get('supplier_payment_type'),
                        //                        'number_of_po' => $request->get('number_of_po'),
                        //                        'supply_schedule' => $request->get('supply_schedule'),
                        'order_date' => customDateFormat($request->get('order_date')),
                        'delivery_date' => customDateFormat($request->get('delivery_date')),
                        'delivery_to_outlet' => $request->get('outlet_id') ?? 0,
                        'warehouse_id' => $request->get('warehouse_id') ?? 0,
                        'start_date' => $request->get('start_date'),
                        'end_date' => $request->get('end_date'),
                        'total_qty' => $total_quantity,
                        'total_value' => $total_val_amount,
                        'commission_value' => $total_discount_amount,
                        'total_vat' => $total_vat_amount,
                        'total_free_amount' => $total_free_amount,
                        'total_amount' => $total_amount,
                    ];


                    $purchaseOrder = PurchaseOrder::create($order_inputs);

                    $update_details = OrderRequisitionDetail::whereIn('id', $order_details_ids)->update(['purchase_order_id' => $purchaseOrder->id]);
                }

                if (count($products) == count($order_product_array)) {
                    $order_status = 2;
                } else {
                    $order_status = 1;
                }
                $requisition_update = $storeRequisition->update(['order_status' => $order_status]);

                DB::commit();
                return $this->sendSuccess('Purchase Order Generate Successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        } else {
            return $this->sendError('Please Select Supplier for all checked product');
        }
    }


    // Status Method
    public function approveStatusName($approve_status)
    {
        switch ($approve_status) {
            case 0:
                $approve_status_name = '<span class="badge bg-warning">Pending</span>';
                break;
            case 1:
                $approve_status_name = '<span class="badge bg-success">Approved</span>';
                break;
            case 2:
                $approve_status_name = '<span class="badge bg-danger">Reject</span>';
                break;
            default:
                $approve_status_name = '<span class="badge bg-default">N/A</span>';
                break;
        }

        return $approve_status_name;
    }

    // Get Store Requisition No
    public function getStoreRequisitionNo()
    {
        $requisition_no = $this->returnStoreRequisitionNo(1);

        $data = [
            'requisition_no' => $requisition_no,
            'current_date'  => date("m/d/Y")
        ];
        return $this->sendResponse($data, 'Requisition no get successfully');
    }
}