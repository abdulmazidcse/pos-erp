<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\StockProduct;
use App\Models\StockProductsLog;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\WarehouseStockProduct;
use App\Models\WarehouseStockProductLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferProductsStockAPIController extends AppBaseController
{

    public function index()
    {
        $stock_transfers = StockTransfer::orderBy('transfer_date', 'DESC')->get();

        $return_data    = $stock_transfers->map(function ($item) {

            if($item->transfer_type == "WTO"){
                $from_transfer = $item->from_warehouse->name.' (Warehouse)';
                $to_transfer    = $item->to_outlet->name.' (Outlet)';
            }elseif($item->transfer_type == "OTO") {
                $from_transfer = $item->from_outlet->name.' (Outlet)';
                $to_transfer    = $item->to_outlet->name.' (Outlet)';
            }else{
                $from_transfer = 'N/A';
                $to_transfer    = 'N/A';
            }
            return [
                'id'    => $item->id,
                'reference_no'  => $item->reference_no,
//                'transfer_date' => $item->transfer_date,
                'transfer_date' => date("Y-m-d", strtotime($item->created_at)),
                'transfer_from' => $from_transfer,
                'transfer_to'   => $to_transfer,
                'total_item'    => $item->total_item,
                'total_quantity'=> $item->total_quantity,
                'total_cost'   => $item->total_cost,
                'shipping_cost'   => $item->shipping_cost,
                'grand_total'   => $item->grand_total,
                'created_at'    => $item->created_at,
                'updated_at'    => $item->updated_at

            ];
        });
        return $this->sendResponse($return_data, 'Transfer data retrieve successfully!');
    }


    public function show($id)
    {
        $stock_transfers = StockTransfer::find($id);

        if(empty($stock_transfers)){
            return $this->sendError('Stock Transfer Data Not Found');
        }

        $transfer_product_array = [];
        if(count($stock_transfers->stock_transfer_details) > 0) {
            foreach($stock_transfers->stock_transfer_details as $product) {
                $transfer_product_array[]   = [
                    'product_id'    => $product->product_id,
                    'product_name'  => $product->product->product_name,
                    'product_code'  => $product->product->product_code,
                    'quantity'  => $product->quantity,
                    'net_unit_cost' => $product->net_unit_cost,
                    'total_amount'  => $product->total_amount,
                ];
            }
        }


        if($stock_transfers->transfer_type == "WTO"){
            $from_transfer = $stock_transfers->from_warehouse->name.' (Warehouse)';
            $to_transfer    = $stock_transfers->to_outlet->name.' (Outlet)';
            $title = $from_transfer.' to '.$to_transfer;
        }elseif($stock_transfers->transfer_type == "OTO") {
            $from_transfer = $stock_transfers->from_outlet->name.' (Outlet)';
            $to_transfer    = $stock_transfers->to_outlet->name.' (Outlet)';
            $title = $from_transfer.' to '.$to_transfer;
        }else{
            $from_transfer = 'N/A';
            $to_transfer    = 'N/A';
            $title = '';
        }

        $return_data    = [
            'transfer_data' => [
                'id'    => $stock_transfers->id,
                'reference_no'  => $stock_transfers->reference_no,
                'transfer_date' => $stock_transfers->transfer_date,
                'transfer_from' => $from_transfer,
                'transfer_to'   => $to_transfer,
                'total_item'    => $stock_transfers->total_item,
                'total_quantity'=> $stock_transfers->total_quantity,
                'total_cost'   => $stock_transfers->total_cost,
                'shipping_cost'   => $stock_transfers->shipping_cost,
                'grand_total'   => $stock_transfers->grand_total,
                'created_at'    => $stock_transfers->created_at,
                'updated_at'    => $stock_transfers->updated_at,
                'title'     => $title,

            ],
            'transfer_products' => $transfer_product_array
        ];
        return $this->sendResponse($return_data, 'Transfer data retrieve successfully!');
    }

    // Warehouse to Outlet Stock Transfer
    public function warehouseToOutletTransfer(Request $request)
    {

        $this->validate($request, [
            'warehouse_id'     => 'required',
            'outlet_id'  => 'required',
            'transfer_date' => 'required',
        ]);

        $products = json_decode($request->get('transfer_products'));

        $total_item = 0;
        $total_quantity = 0;
        $total_weight   = 0;
        $total_cost = 0;
        $shipping_cost = 0;
        $transfer_product_array = [];
        $stock_product_details_array = [];
        if(count($products) > 0) {
            foreach ($products as $product) {

                if($product->checked) {
                    $total_product_amount = ($product->purchase_price * $product->transfer_quantity);
                    $stock_product_details_array[] = new StockTransferDetail([
                        'stock_id'  => $product->id,
                        'product_id' => $product->product_id,
                        'quantity'  => $product->transfer_quantity,
                        'weight'    => $product->transfer_weight,
                        'unit_id'   => $product->unit_id,
                        'net_unit_cost' => $product->purchase_price,
                        'total_amount'  => $total_product_amount,
                        'created_at'    => Carbon::now()->toDateTimeString(),
                        'updated_at'    => Carbon::now()->toDateTimeString()
                    ]);

                    $total_item = $total_item + 1;
                    $total_quantity += $product->transfer_quantity;
                    $total_weight += $product->transfer_weight;
                    $total_cost += $total_product_amount;

                    $transfer_product_array[] = $product;
                }
            }
        }

        if(empty($transfer_product_array)) {
            return $this->sendError('Please select/checked at least one products');
        }

        $warehouse_id = $request->get('warehouse_id');
        $outlet_id  = $request->get('outlet_id');
        $transfer_date = $request->get('transfer_date');

        $generate_reference_no  = $this->returnStockTransferReferenceNo();
        $stock_transfer_inputs = [
            'reference_no'  => $generate_reference_no,
            'transfer_type' => 'WTO',
            'from_warehouse_id' => $warehouse_id,
            'to_outlet_id'  => $outlet_id,
            'total_item'    => $total_item,
            'total_quantity'    => $total_quantity,
            'total_weight'    => $total_weight,
            'total_cost'    => $total_cost,
            'shipping_cost' => $shipping_cost,
            'grand_total'   => $total_cost + $shipping_cost,
            'transfer_date' => $transfer_date

        ];

        DB::beginTransaction();

        try{

            $stock_transfer = StockTransfer::create($stock_transfer_inputs);
            $stock_transfer_details = $stock_transfer->stock_transfer_details()->saveMany($stock_product_details_array);

            foreach($transfer_product_array as $transfer_product){

                $warehouse_stock_id = $transfer_product->id;
                $product_id = $transfer_product->product_id;
                $expires_date = $transfer_product->expires_date;
                $new_quantity = $transfer_product->transfer_quantity;
                $new_weight = $transfer_product->transfer_weight;
                // Product Stock Insert and Update with expires date
                if($expires_date != "") {
                    $stock_product = StockProduct::where('product_id', $product_id)->where('outlet_id', $outlet_id)->whereDate('expires_date', $expires_date)->first();
                    if(!empty($stock_product)) {
                        $update_input = [
                            'in_stock_quantity' => ($stock_product->in_stock_quantity + $new_quantity),
                            'in_stock_weight' => ($stock_product->in_stock_weight + $new_weight),
                            'stock_quantity' => ($stock_product->stock_quantity + $new_quantity),
                            'stock_weight' => ($stock_product->stock_weight + $new_weight)
                        ];

                        $stock_product->update($update_input);

                    }else{
                        $new_inputs = [
                            'product_id'    => $product_id,
                            'outlet_id'     => $outlet_id,
                            'in_stock_quantity' => $new_quantity,
                            'stock_quantity'    => $new_quantity,
                            'in_stock_weight' => $new_weight,
                            'stock_weight'    => $new_weight,
                            'expires_date'  => $expires_date,
                        ];

                        $stock_product = StockProduct::create($new_inputs);
                    }
                }
                // Product Stock Insert and Update Without expires date
                else{
                    $stock_product = StockProduct::where('product_id', $product_id)->where('outlet_id', $outlet_id)->first();
                    if(!empty($stock_product)) {
                        $update_input = [
                            'in_stock_quantity' => ($stock_product->in_stock_quantity + $new_quantity),
                            'in_stock_weight' => ($stock_product->in_stock_weight + $new_weight),
                            'stock_quantity' => ($stock_product->stock_quantity + $new_quantity),
                            'stock_weight' => ($stock_product->stock_weight + $new_weight)
                        ];

                        $stock_product->update($update_input);

                    }else{
                        $new_inputs = [
                            'product_id'    => $product_id,
                            'outlet_id'     => $outlet_id,
                            'in_stock_quantity' => $new_quantity,
                            'stock_quantity'    => $new_quantity,
                            'in_stock_weight' => $new_weight,
                            'stock_weight'    => $new_weight,
                        ];

                        $stock_product = StockProduct::create($new_inputs);
                    }
                }

                // Stock Product Log
                $stock_log_inputs  = [
                    'product_id' => $product_id,
                    'outlet_id' => $outlet_id,
                    'in_stock_quantity' => $new_quantity,
                    'stock_quantity'    => $stock_product->stock_quantity + $new_quantity,
                    'out_stock_quantity'    => 0,
                    'in_stock_weight' => $new_weight,
                    'stock_weight'    => $stock_product->stock_weight + $new_weight,
                    'out_stock_weight'    => 0,
                    'expires_date'  => $expires_date ?? NULL,
                    'stock_type'    => 'WTO',
                    'note'  => 'Transfer Stock In',
                    'user_id'   => auth()->user()->id,
                ];

                $stock_log_save = StockProductsLog::create($stock_log_inputs);

                //Warehouse Stock Update
                $warehouse_stock_product = WarehouseStockProduct::find($warehouse_stock_id);
                $update_data = [
                    'stock_quantity'    => ($warehouse_stock_product->stock_quantity - $new_quantity),
                    'out_stock_quantity'    => ($warehouse_stock_product->out_stock_quantity + $new_quantity),
                    'stock_weight'    => ($warehouse_stock_product->stock_weight - $new_weight),
                    'out_stock_weight'    => ($warehouse_stock_product->out_stock_weight + $new_weight)
                ];

                $warehouse_stock_product->update($update_data);

                // Warehouse Stock Log
                $whstock_log_inputs  = [
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'in_stock_quantity' => 0,
                    'stock_quantity'    => $stock_product->stock_quantity - $new_quantity,
                    'out_stock_quantity'    => $new_quantity,
                    'in_stock_weight' => 0,
                    'stock_weight'    => $stock_product->stock_weight - $new_weight,
                    'out_stock_weight'    => $new_weight,
                    'expires_date'  => $expires_date ?? NULL,
                    'stock_type'    => 'WTO',
                    'note'  => 'Transfer Stock Out',
                    'user_id'   => auth()->user()->id,
                ];

                $whstock_log_save   = WarehouseStockProductLog::create($whstock_log_inputs);



            }

            DB::commit();
            return $this->sendResponse($stock_transfer, 'Stock Transfer Successfully Done!');

        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }


    }


    public function getWarehouseStockProducts(Request $request)
    {
        $warehouse_id   = $request->get('warehouse_id');

        $stock_products = WarehouseStockProduct::where('warehouse_id', $warehouse_id)
                                                ->where('stock_quantity', '>', 0)
                                                ->get();

        $return_data = $stock_products->map(function ($item) {
            return [
                'id'    => $item->id,
                'product_id'    => $item->product_id,
                'product_name'  => $item->product->product_name,
                'product_code'  => $item->product->product_code,
                'unit_code'     => $item->product->purchase_unit->unit_code,
                'unit_id'       => $item->product->purchase_unit->id,
                'purchase_price'=> $item->product->cost_price,
                'mrp_price'     => $item->product->mrp_price,
                'stock_quantity'=> $item->stock_quantity,
                'stock_weight'  => $item->stock_weight,
                'transfer_quantity' => $item->stock_quantity,
                'transfer_weight'  => $item->stock_weight,
                'expires_date'  => $item->expires_date,
                'checked'   => false,
            ];

        })->toArray();

        return $this->sendResponse($return_data, 'Products Retrieve Successfully');
    }



    // Outlet to Outlet Stock Transfer
    public function outletToOutletTransfer(Request $request){
//        return response()->json($request->all());

        $this->validate($request, [
            'from_outlet_id'     => 'required',
            'to_outlet_id'  => 'required',
            'transfer_date' => 'required',
        ]);

        $products = json_decode($request->get('transfer_products'));

        $total_item = 0;
        $total_quantity = 0;
        $total_weight = 0;
        $total_cost = 0;
        $shipping_cost = 0;
        $transfer_product_array = [];
        $stock_product_details_array = [];
        if(count($products) > 0) {
            foreach ($products as $product) {

                if($product->checked) {
                    $total_product_amount = ($product->purchase_price * $product->transfer_quantity);
                    $stock_product_details_array[] = new StockTransferDetail([
                        'stock_id'  => $product->id,
                        'product_id' => $product->product_id,
                        'quantity'  => $product->transfer_quantity,
                        'weight'  => $product->transfer_weight,
                        'unit_id'   => $product->unit_id,
                        'net_unit_cost' => $product->purchase_price,
                        'total_amount'  => $total_product_amount,
                        'created_at'    => Carbon::now()->toDateTimeString(),
                        'updated_at'    => Carbon::now()->toDateTimeString()
                    ]);

                    $total_item = $total_item + 1;
                    $total_quantity += $product->transfer_quantity;
                    $total_weight += $product->transfer_weight;
                    $total_cost += $total_product_amount;

                    $transfer_product_array[] = $product;
                }
            }
        }

        if(empty($transfer_product_array)) {
            return $this->sendError('Please select/checked at least one products');
        }

        $from_outlet_id = $request->get('from_outlet_id');
        $to_outlet_id  = $request->get('to_outlet_id');
        $transfer_date = $request->get('transfer_date');

        $generate_reference_no  = $this->returnStockTransferReferenceNo();
        $stock_transfer_inputs = [
            'reference_no'  => $generate_reference_no,
            'transfer_type' => 'OTO',
            'from_outlet_id'=> $from_outlet_id,
            'to_outlet_id'  => $to_outlet_id,
            'total_item'    => $total_item,
            'total_quantity'=> $total_quantity,
            'total_weight'=> $total_weight,
            'total_cost'    => $total_cost,
            'shipping_cost' => $shipping_cost,
            'grand_total'   => $total_cost + $shipping_cost,
            'transfer_date' => $transfer_date

        ];

        DB::beginTransaction();

        try{

            $stock_transfer = StockTransfer::create($stock_transfer_inputs);
            $stock_transfer_details = $stock_transfer->stock_transfer_details()->saveMany($stock_product_details_array);

            foreach($transfer_product_array as $transfer_product){

                $outlet_stock_id = $transfer_product->id;
                $product_id = $transfer_product->product_id;
                $expires_date = $transfer_product->expires_date;
                $new_quantity = $transfer_product->transfer_quantity;
                $new_weight = $transfer_product->transfer_weight;

                // Product Stock Insert and Update with expires date
                if($expires_date != "") {
                    $stock_product = StockProduct::where('product_id', $product_id)->where('outlet_id', $to_outlet_id)->whereDate('expires_date', $expires_date)->first();
                    if(!empty($stock_product)) {
                        $update_input = [
                            'in_stock_quantity' => ($stock_product->in_stock_quantity + $new_quantity),
                            'in_stock_weight' => ($stock_product->in_stock_weight + $new_weight),
                            'stock_quantity' => ($stock_product->stock_quantity + $new_quantity),
                            'stock_weight' => ($stock_product->stock_weight + $new_weight)
                        ];

                        $stock_product->update($update_input);

                    }else{
                        $new_inputs = [
                            'product_id'    => $product_id,
                            'outlet_id'     => $to_outlet_id,
                            'in_stock_quantity' => $new_quantity,
                            'stock_quantity'    => $new_quantity,
                            'in_stock_weight' => $new_weight,
                            'stock_weight'    => $new_weight,
                            'expires_date'  => $expires_date,
                        ];

                        $stock_product = StockProduct::create($new_inputs);
                    }
                }
                // Product Stock Insert and Update Without expires date
                else{
                    $stock_product = StockProduct::where('product_id', $product_id)->where('outlet_id', $to_outlet_id)->first();
                    if(!empty($stock_product)) {
                        $update_input = [
                            'in_stock_quantity' => ($stock_product->in_stock_quantity + $new_quantity),
                            'in_stock_weight' => ($stock_product->in_stock_weight + $new_weight),
                            'stock_quantity' => ($stock_product->stock_quantity + $new_quantity),
                            'stock_weight' => ($stock_product->stock_weight + $new_weight) 
                        ];

                        $stock_product->update($update_input);

                    }else{
                        $new_inputs = [
                            'product_id'    => $product_id,
                            'outlet_id'     => $to_outlet_id,
                            'in_stock_quantity' => $new_quantity,
                            'stock_quantity'    => $new_quantity,
                            'in_stock_weight' => $new_weight,
                            'stock_weight'    => $new_weight,
                        ];

                        $stock_product = StockProduct::create($new_inputs);
                    }
                }

                // In Stock Product Log
                $in_stock_log_inputs  = [
                    'product_id' => $product_id,
                    'outlet_id' => $to_outlet_id,
                    'in_stock_quantity' => $new_quantity,
                    'stock_quantity'    => $stock_product->stock_quantity + $new_quantity,
                    'out_stock_quantity'    => 0,
                    'in_stock_weight' => $new_weight,
                    'stock_weight'    => $stock_product->stock_weight + $new_weight,
                    'out_stock_weight'    => 0,
                    'expires_date'  => $expires_date ?? NULL,
                    'stock_type'    => 'OTO',
                    'note'  => 'Transfer product',
                    'user_id'   => auth()->user()->id,
                ];

                $stock_product_log_save = StockProductsLog::create($in_stock_log_inputs);


                //Outlet Stock Update
                $outlet_stock_product = StockProduct::find($outlet_stock_id);
                $update_data = [
                    'stock_quantity'    => ($outlet_stock_product->stock_quantity - $new_quantity),
                    'out_stock_quantity'    => ($outlet_stock_product->out_stock_quantity + $new_quantity),
                    'stock_weight'    => ($outlet_stock_product->stock_weight - $new_weight),
                    'out_stock_weight'    => ($outlet_stock_product->out_stock_weight + $new_weight),
                ];

                $outlet_stock_product->update($update_data);

                // Out Stock Product Log
                $out_stock_log_inputs  = [
                    'product_id' => $product_id,
                    'outlet_id' => $from_outlet_id,
                    'in_stock_quantity' => 0,
                    'stock_quantity'    => $stock_product->stock_quantity - $new_quantity,
                    'out_stock_quantity'    => $new_quantity,
                    'in_stock_weight' => 0,
                    'stock_weight'    => $stock_product->stock_weight - $new_weight,
                    'out_stock_weight'    => $new_weight,
                    'expires_date'  => $expires_date ?? NULL,
                    'stock_type'    => 'OTO',
                    'note'  => 'Transfer Stock OUT',
                    'user_id'   => auth()->user()->id,
                ];

                $stock_out_log_save = StockProductsLog::create($out_stock_log_inputs);

            }

            DB::commit();
            return $this->sendResponse($stock_transfer, 'Stock Transfer Successfully Done!');

        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }


    // Outlet to Warehouse Stock Transfer
    public function outletToWarehouseTransfer(Request $request){
//        return response()->json($request->all());

        $this->validate($request, [
            'otw_outlet_id'     => 'required',
            'otw_warehouse_id'  => 'required',
            'transfer_date' => 'required',
        ]);

        $products = json_decode($request->get('transfer_products'));

        $total_item = 0;
        $total_quantity = 0;
        $total_weight = 0;
        $total_cost = 0;
        $shipping_cost = 0;
        $transfer_product_array = [];
        $stock_product_details_array = [];
        if(count($products) > 0) {
            foreach ($products as $product) {

                if($product->checked) {
                    $total_product_amount = ($product->purchase_price * $product->transfer_quantity);
                    $stock_product_details_array[] = new StockTransferDetail([
                        'stock_id'  => $product->id,
                        'product_id' => $product->product_id,
                        'quantity'  => $product->transfer_quantity,
                        'weight'  => $product->transfer_weight,
                        'unit_id'   => $product->unit_id,
                        'net_unit_cost' => $product->purchase_price,
                        'total_amount'  => $total_product_amount,
                        'created_at'    => Carbon::now()->toDateTimeString(),
                        'updated_at'    => Carbon::now()->toDateTimeString()
                    ]);

                    $total_item = $total_item + 1;
                    $total_quantity += $product->transfer_quantity;
                    $total_weight += $product->transfer_weight;
                    $total_cost += $total_product_amount;

                    $transfer_product_array[] = $product;
                }
            }
        }

        if(empty($transfer_product_array)) {
            return $this->sendError('Please select/checked at least one products');
        }

        $otw_outlet_id = $request->get('otw_outlet_id');
        $otw_warehouse_id  = $request->get('otw_warehouse_id');
        $transfer_date = $request->get('transfer_date');

        $generate_reference_no  = $this->returnStockTransferReferenceNo();
        $stock_transfer_inputs = [
            'reference_no'  => $generate_reference_no,
            'transfer_type' => 'OTW',
            'from_outlet_id'=> $otw_outlet_id,
            'to_warehouse_id'  => $otw_warehouse_id,
            'total_item'    => $total_item,
            'total_quantity'=> $total_quantity,
            'total_weight'=> $total_weight,
            'total_cost'    => $total_cost,
            'shipping_cost' => $shipping_cost,
            'grand_total'   => $total_cost + $shipping_cost,
            'transfer_date' => $transfer_date

        ];

        DB::beginTransaction();

        try{

            $stock_transfer = StockTransfer::create($stock_transfer_inputs);
            $stock_transfer_details = $stock_transfer->stock_transfer_details()->saveMany($stock_product_details_array);

            foreach($transfer_product_array as $transfer_product){

                $outlet_stock_id = $transfer_product->id;
                $product_id = $transfer_product->product_id;
                $expires_date = $transfer_product->expires_date;
                $new_quantity = $transfer_product->transfer_quantity;
                $new_weight = $transfer_product->transfer_weight;

                // Product Stock Insert and Update with expires date
                if($expires_date != "") {
                    $stock_product = WarehouseStockProduct::where('product_id', $product_id)->where('warehouse_id', $otw_warehouse_id)->whereDate('expires_date', $expires_date)->first();
                    if(!empty($stock_product)) {
                        $update_input = [
                            'in_stock_quantity' => ($stock_product->in_stock_quantity + $new_quantity),
                            'in_stock_weight' => ($stock_product->in_stock_weight + $new_weight),
                            'stock_quantity' => ($stock_product->stock_quantity + $new_quantity),
                            'stock_weight' => ($stock_product->stock_weight + $new_weight)
                        ];

                        $stock_product->update($update_input);

                    }else{
                        $new_inputs = [
                            'product_id'    => $product_id,
                            'warehouse_id'     => $otw_warehouse_id,
                            'in_stock_quantity' => $new_quantity,
                            'stock_quantity'    => $new_quantity,
                            'in_stock_weight' => $new_weight,
                            'stock_weight'    => $new_weight,
                            'expires_date'  => $expires_date,
                        ];

                        $stock_product = WarehouseStockProduct::create($new_inputs);
                    }
                }
                // Product Stock Insert and Update Without expires date
                else{
                    $stock_product = WarehouseStockProduct::where('product_id', $product_id)->where('warehouse_id', $otw_warehouse_id)->first();
                    if(!empty($stock_product)) {
                        $update_input = [
                            'in_stock_quantity' => ($stock_product->in_stock_quantity + $new_quantity),
                            'in_stock_weight' => ($stock_product->in_stock_weight + $new_weight),
                            'stock_quantity' => ($stock_product->stock_quantity + $new_quantity),
                            'stock_weight' => ($stock_product->stock_weight + $new_weight)
                        ];

                        $stock_product->update($update_input);

                    }else{
                        $new_inputs = [
                            'product_id'    => $product_id,
                            'warehouse_id'     => $otw_warehouse_id,
                            'in_stock_quantity' => $new_quantity,
                            'stock_quantity'    => $new_quantity,
                            'in_stock_weight' => $new_weight,
                            'stock_weight'    => $new_weight,
                        ];

                        $stock_product = WarehouseStockProduct::create($new_inputs);
                    }
                }

                // Stock Product Log
                $in_stock_log_inputs  = [
                    'product_id' => $product_id,
                    'warehouse' => $otw_warehouse_id,
                    'in_stock_quantity' => $new_quantity,
                    'stock_quantity'    => $stock_product->stock_quantity + $new_quantity,
                    'out_stock_quantity'    => 0,
                    'in_stock_weight' => $new_weight,
                    'stock_weight'    => $stock_product->stock_weight + $new_weight,
                    'out_stock_weight'    => 0,
                    'expires_date'  => $expires_date ?? NULL,
                    'stock_type'    => 'OTW',
                    'note'  => 'Transfer Stock In',
                    'user_id'   => auth()->user()->id,
                ];

                $stock_in_log_save = WarehouseStockProductLog::create($in_stock_log_inputs);

                //Outlet Stock Update
                $outlet_stock_product = StockProduct::find($outlet_stock_id);
                $update_data = [
                    'stock_quantity'    => ($outlet_stock_product->stock_quantity - $new_quantity),
                    'out_stock_quantity'    => ($outlet_stock_product->out_stock_quantity + $new_quantity),
                    'stock_weight'    => ($outlet_stock_product->stock_weight - $new_weight),
                    'out_stock_weight'    => ($outlet_stock_product->out_stock_weight + $new_weight),
                ];

                $outlet_stock_product->update($update_data);

                // Stock Product Log
                $out_stock_log_inputs  = [
                    'product_id' => $product_id,
                    'outlet_id' => $otw_outlet_id,
                    'in_stock_quantity' => 0,
                    'stock_quantity'    => $stock_product->stock_quantity - $new_quantity,
                    'out_stock_quantity'    => $new_quantity,
                    'in_stock_weight' => 0,
                    'stock_weight'    => $stock_product->stock_weight - $new_weight,
                    'out_stock_weight'    => $new_weight,
                    'expires_date'  => $expires_date ?? NULL,
                    'stock_type'    => 'OTW',
                    'note'  => 'Transfer Stock Out',
                    'user_id'   => auth()->user()->id,
                ];

                $stock_out_log_save = StockProductsLog::create($out_stock_log_inputs);

            }

            DB::commit();
            return $this->sendResponse($stock_transfer, 'Stock Transfer Successfully Done!');

        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function getOutletStockProducts(Request $request)
    {
        $outlet_id   = $request->get('outlet_id');

        $stock_products = StockProduct::where('outlet_id', $outlet_id)
            ->where(function($q) {  
                $q->where('stock_quantity', '>', 0)
                ->orWhere('stock_weight', '>', 0);
            })  
            ->get();

        $return_data = $stock_products->map(function ($item) {
            return [
                'id'    => $item->id,
                'product_id'    => $item->product_id,
                'product_name'  => $item->product->product_name,
                'product_code'  => $item->product->product_code,
                'unit_code'     => $item->product->purchase_unit->unit_code,
                'unit_id'       => $item->product->purchase_unit->id,
                'purchase_price'=> $item->product->cost_price,
                'mrp_price'     => $item->product->mrp_price,
                'stock_quantity'=> $item->stock_quantity,
                'stock_weight'  => $item->stock_weight,
                'transfer_quantity' => $item->stock_quantity,
                'transfer_weight'  => $item->stock_weight,
                'expires_date'  => $item->expires_date,
                'checked'   => false,
            ];
        })->toArray();

        return $this->sendResponse($return_data, 'Products Retrieve Successfully');
    }
}
