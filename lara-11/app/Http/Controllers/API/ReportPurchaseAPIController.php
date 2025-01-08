<?php

namespace App\Http\Controllers\API;

use App\Exports\Export;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\ProductReportPurchaseResource;
use App\Http\Resources\ReportPurchaseResource;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveDetail;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportPurchaseAPIController extends AppBaseController
{
    public function getPurchaseReport(Request $request)
    {
        $columns = ['id', 'product_id', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");
        $query = PurchaseReceive::with(['suppliers'])->where('status', 1)->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('reference_no', 'like', '%' .$searchValue. '%');
                $query->orWhere('purchase_date', 'like', '%' .$searchValue. '%');
                $query->orWhere('net_amount', 'like', '%' .$searchValue. '%');
                $query->orWhereHas('suppliers', function ($query) use ($searchValue) {
                    $query->where('name', 'like', '%' .$searchValue. '%');
                });

            });
        }

        if(isset($from_date)) {
            $query->where(function($query) use ($from_date) {
                $query->whereDate('purchase_date', '>=', $from_date);
            });
        }

        if(isset($to_date)) {
            $query->where(function($query) use ($to_date) {
                $query->whereDate('purchase_date', '<=', $to_date);
            });
        }

        $purchase_data = $query->paginate($length);
        $return_data    = [
            'data' => $purchase_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];
        return $this->sendResponse($return_data, 'Purchase retrieved successfully');
    }


    public function getProductPurchaseReport(Request $request)
    {
        $columns = ['id', 'product_id', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");
        $product_id = request('product_id');
        $supplier_id = request('supplier_id');

        if($supplier_id != "" && $supplier_id != 0) {
            $suplier_data = Supplier::find($supplier_id);
        }else{
            $suplier_data = "";
        }

        $query = PurchaseReceiveDetail::with(['purchase_receive', 'products', 'suppliers'])->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('purchase_receive', function ($query) use ($searchValue) {
                    $query->where('purchase_date', 'like', '%' .$searchValue. '%');
                });
                $query->orWhere('receive_purchase_price', 'like', '%' .$searchValue. '%');
                $query->orWhereHas('suppliers', function ($query) use ($searchValue) {
                    $query->where('name', 'like', '%' .$searchValue. '%');
                });

            });
        }

        if(isset($product_id)) {
            $query->where(function($query) use ($product_id) {
                $query->where('receive_product_id', $product_id);
            });
        }

        if(isset($supplier_id)) {
            $query->where(function($query) use ($supplier_id) {
                $query->where('receive_supplier_id', $supplier_id);
            });
        }

        if(isset($from_date)) {
            $query->where(function($query) use ($from_date) {
                $query->whereHas('purchase_receive', function ($query) use ($from_date) {
                    $query->whereDate('purchase_date', '>=', $from_date);
                });
            });
        }

        if(isset($to_date)) {
            $query->where(function($query) use ($to_date) {
                $query->whereHas('purchase_receive', function ($query) use ($to_date) {
                    $query->whereDate('purchase_date', '<=', $to_date);
                });
            });
        }

        $purchase_data = $query->paginate($length);
        $return_data    = [
            'data' => $purchase_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
            'supplier'  => $suplier_data
        ];
        return $this->sendResponse($return_data, 'Purchase retrieved successfully');
    }


    public function excelPurchasesExportCollection($request){ 

        // Call the index method to retrieve the JSON response
        $response = $this->getPurchaseReport($request);   
        
        // Get the data array from the JSON response
        $data = $response->getData(); 

        // Now $data contains the array of data 
        $return_data = ReportPurchaseResource::collection($data->data->data->data); 

        return $this->sendResponse($return_data,'');
    }
    public function getPurchaseReportExcelExport(Request $request){ 
        $response = $this->excelPurchasesExportCollection($request);

        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data;  

        $customHeadings = [ ['Purchase Report'],['From '.$from_date.' TO '.$to_date ]];
        $columns = ['Sl', 'purchase_date','reference_no', 'suppliers','net_amount']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:E1';
        $margeRangeTwo = 'A2:E2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'purchase-report.xlsx');
    } 
    public function excelProductPurchaseExportCollection($request){ 

        // Call the index method to retrieve the JSON response
        $response = $this->getProductPurchaseReport($request);   
        
        // Get the data array from the JSON response
        $data = $response->getData(); 

        // Now $data contains the array of data 
        $return_data = ProductReportPurchaseResource::collection($data->data->data->data); 

        return $this->sendResponse($return_data,'');
    }
    public function getProductPurchaseReportExcelExport(Request $request){ 
        $response = $this->excelProductPurchaseExportCollection($request);

        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data;  

        $customHeadings = [ ['Product Purchase Report'],['From '.$from_date.' TO '.$to_date ]];
        $columns = ['Sl', 'purchase_date','supplier', 'product','purchase_price','qty','weight','amount']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:H1';
        $margeRangeTwo = 'A2:H2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'product-purchase-report.xlsx');
    } 

}
