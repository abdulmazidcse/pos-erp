<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\InventoryDetailsResource;
use App\Http\Resources\LowStockReportResource;
use App\Http\Resources\StockReportResource;
use App\Http\Resources\StockReportExportResource;
use App\Models\PaymentCollection;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveDetail;
use App\Models\Sale;
use App\Models\StockProduct;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\Export; 
use App\Exports\StockExport; 
use Maatwebsite\Excel\Facades\Excel;

class ReportAPIController extends AppBaseController
{
    public function getStockReport(Request $request)
    {
        $columns = ['id', 'product_id', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $outlet_id = request('outlet_id');
        $category_id = request('category_id');
        $product_id = request('product_id');
        // $query = StockProduct::with(['product'])->orderBy($columns[$column], $dir);
        $query = StockProduct::with(['product'])->when($column, function($query) use ( $columns, $column, $dir){
            return $query->orderBy($columns[$column], $dir);
        });

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('product', function ($query) use ($searchValue) {
                    $query->where('product_name', 'like', '%' .$searchValue. '%');
                    $query->orWhere('product_code', 'like', '%' .$searchValue. '%');
                });

            });
        }

        if(isset($outlet_id)) {
            $query->where(function($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id);
            });
        }

        if(isset($category_id)) {
            $query->where(function($query) use ($category_id) {
                $query->whereHas('product', function ($query) use ($category_id) {
                    $query->where('sub_category_id', $category_id);
                });
            });
        }

        if(isset($product_id)) {
            $query->where(function($query) use ($product_id) {
                $query->where('product_id', $product_id);
            });
        }
        $query->where(function($query) {
            $query->whereHas('product', function ($q) {
//                $q->
            });
        });

        $stock_products = $query->paginate($length);
        $units = Unit::all();

        $purchase_receive   = PurchaseReceive::orderBy('id', 'desc');
        if(isset($outlet_id)) {
            $purchase_receive->where(function($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id);
            });
        }

        $purchase_total_amount      = $purchase_receive->sum('total_amount');
        $purchase_net_amount        = $purchase_receive->sum('net_amount');

        $discount_amount    = $purchase_total_amount - $purchase_net_amount;
        $return_data    = [
            'data' => $stock_products,
            'draw' => $request->input('draw'),
            'unit_data' => $units,
            'discount_amount'   => $discount_amount,
        ];
        return $this->sendResponse($return_data, 'Stock data retrieved successfully');
    }


    public function getLowStockReport(Request $request)
    {
        $columns = ['id', 'product_id', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $category_id = request('category_id');
        $product_id = request('product_id');
        $query = StockProduct::with(['product'])->where('stock_quantity', '<=', 10)->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('product', function ($query) use ($searchValue) {
                    $query->where('product_name', 'like', '%' .$searchValue. '%');
                    $query->orWhere('product_code', 'like', '%' .$searchValue. '%');
                });

            });
        }

        if(isset($category_id)) {
            $query->where(function($query) use ($category_id) {
                $query->whereHas('product', function ($query) use ($category_id) {
                    $query->where('sub_category_id', $category_id);
                });
            });
        }

        if(isset($product_id)) {
            $query->where(function($query) use ($product_id) {
                $query->where('product_id', $product_id);
            });
        }

        $stock_products = $query->paginate($length);
        $return_data    = [
            'data' => $stock_products,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Stock data retrieved successfully');
    }


    public function getDailySummaryReport(Request $request)
    {
        $outlet_id = $request->get('outlet_id');
        $src_date   = $request->get('src_date') ?? Carbon::now()->format("Y-m-d");

        // Purchase Report
        $purchase_query = PurchaseReceive::where('outlet_id', $outlet_id)->with(['suppliers'])->where('status', 1)->orderBy("id", 'desc');

        // Sales Report
        $sale_query     = Sale::where('outlet_id', $outlet_id)->with(['customer'])->orderBy('id', 'desc');

        // Collections Query
        $collection_query = PaymentCollection::with(['sales'])->orderBy('id', 'desc');

        if(isset($src_date)) {
            $purchase_query->where(function($query) use ($src_date) {
                $query->whereDate('purchase_date', '=', $src_date);
            });

            $sale_query->where(function($query) use ($src_date) {
                $query->whereDate('created_at', '=', $src_date);
            });

            $collection_query->where(function($query) use ($src_date) {
                $query->whereDate('created_at', '=', $src_date);
            });
        }

        $purchase_data  = $purchase_query->get();
        $sales_data = $sale_query->get();
        $collection_data = $collection_query->get();

        $return_data    = [
            'data' => [
                'purchase_items'    => $purchase_data,
                'sales_items'    => $sales_data,
                'collection_items'  => $collection_data,
            ],
            'src_date' => $src_date,
        ];

        return $this->sendResponse($return_data, "Data retrieve successfully!");

    }

    public function stockReportResourceCollection($request){ 

        // Call the index method to retrieve the JSON response
        $response = $this->getStockReport($request);  

        // Get the data array from the JSON response
        $data = $response->getData();

        // Now $data contains the array of data 
        // $return_suppliers = StockReportResource::collection($data->data->data->data); 
        if($request->get('export')){
            $return_suppliers = StockReportExportResource::collection($data->data->data->data);
        } else {
            $return_suppliers = StockReportResource::collection($data->data->data->data);
        }

        return $this->sendResponse($return_suppliers, 'Suppliers retrieved successfully');
    }
    public function stockReportExcelExport(Request $request){ 
        $request['export'] = 1;
        $response = $this->stockReportResourceCollection($request);
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data;
        
        // dd($returnData);

        $customHeadings = [ ['Stock Report'],[]];
        $columns = ['SL', 'item','category','mrp_price','cost_price', 'in_stock_quantity','out_stock_quantity','stock_quantity','unit_code','stock_weight_mrp_price','stock_weight_cost_price']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:K1';
        $margeRangeTwo = 'A2:K2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'stock-report.xlsx');
    } 
    
    public function inventoryDetailsReportResourceCollection($request){

        // Call the index method to retrieve the JSON response
        $response = $this->getStockReport($request);

        // Get the data array from the JSON response
        $data = $response->getData();

        // Now $data contains the array of data
        $return_suppliers = InventoryDetailsResource::collection($data->data->data->data);

        return $this->sendResponse($return_suppliers, 'Inventory details retrieved successfully');
    }

    public function inventoryDetailsReportExcelExport(Request $request){
        $response = $this->inventoryDetailsReportResourceCollection($request);
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data;

        $customHeadings = [ ['Inventory Details Report'],[]];
        $columns = ['SL', 'category','item','unit_code', 'cost_price', 'mrp_price','stock_quantity','stock_purchase_amount','stock_sale_amount'];

        // Create an instance of the export class with the data
        $margeRangeOne = 'A1:I1';
        $margeRangeTwo = 'A2:I2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'inventory-details-report.xlsx');
    }
    
    public function lowStockReportExcelExportCollection($request){ 

        // Call the index method to retrieve the JSON response
        $response = $this->getLowStockReport($request);  

        // Get the data array from the JSON response
        $data = $response->getData(); 

        // Now $data contains the array of data 
        $return_data = LowStockReportResource::collection($data->data->data->data); 

        return $this->sendResponse($return_data,'');
    }
    public function lowStockReportExcelExport(Request $request){ 
        $response = $this->lowStockReportExcelExportCollection($request);
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data; 

        $customHeadings = [ ['Low Stock Report'],[]];
        $columns = ['SL', 'item','category', 'Low_Stock_Qty','Stock_Qty']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:E1';
        $margeRangeTwo = 'A2:E2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'low-stock-report.xlsx');
    } 
}
