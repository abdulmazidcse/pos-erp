<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsSalesReportResource;
use App\Http\Resources\SalesReportResource;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PaymentCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\Export;  
use Maatwebsite\Excel\Facades\Excel;

class ReportSalesAPIController extends AppBaseController
{

    public function getDailySalesReport(Request $request)
    {
        $columns = ['id', 'created_at', 'total_amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");
        $query = Sale::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as sales_date"), DB::raw('sum(total_amount) as sales_amount'))
        ->whereNotIn('return_type', [3])->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('created_at', 'like', '%' .$searchValue. '%');
            });
        }

        if(isset($from_date)) {
            $query->where(function($query) use ($from_date) {
                $query->whereDate('created_at', '>=', $from_date);
            });
        }

        if(isset($to_date)) {
            $query->where(function($query) use ($to_date) {
                $query->whereDate('created_at', '<=', $to_date);
            });
        }
        $query->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"));
        $sales_data = $query->paginate($length);
        $return_data    = [
            'data' => $sales_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];
        return $this->sendResponse($return_data, 'Sales retrieved successfully');
    }

    public function getSalesReport(Request $request)
    {
        $columns = ['id', 'customer_id', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $customer_id = request('customer_id');

        $query = Sale::with(['customer'])->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('invoice_number', 'like', '%' .$searchValue. '%');
                $query->orWhere('created_at', 'like', '%' .$searchValue. '%');
                $query->orWhere('grand_total', 'like', '%' .$searchValue. '%');
                $query->orWhereHas('customer', function ($query) use ($searchValue) {
                    $query->where('name', 'like', '%' .$searchValue. '%');
                });

            });
        }

        if(isset($from_date)) {
            $query->where(function($query) use ($from_date) {
                $query->whereDate('created_at', '>=', $from_date);
            });
        }

        if(isset($to_date)) {
            $query->where(function($query) use ($to_date) {
                $query->whereDate('created_at', '<=', $to_date);
            });
        }

        if(isset($customer_id)) {
            $query->where(function($query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            });
        }

        $sales_data = $query->paginate($length);
        $return_data    = [
            'data' => $sales_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];
        return $this->sendResponse($return_data, 'Sales data retrieved successfully');
    }

    public function getCollectionReport(Request $request)
    {
        $columns = ['payment_collections.id', 'payment_collections.created_at', 'invoice_number', 'customer_name', 'paying_by', 'amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $customer_id = request('customer_id');

        $query = PaymentCollection::with(['sales'])
                ->leftJoin('sales', 'sales.id', '=', 'payment_collections.sale_id')
                ->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) { 
                $query->where('payment_collections.created_at', 'like', '%' .$searchValue. '%');
                $query->orWhere('payment_collections.paying_by', 'like', '%' .$searchValue. '%');
                $query->orWhereHas('sales', function ($query) use ($searchValue) {
                    $query->where('customer_name', 'like', '%' .$searchValue. '%');
                });
                $query->orWhereHas('sales', function ($query) use ($searchValue) {
                    $query->where('invoice_number', 'like', '%' .$searchValue. '%');
                });
                $query->orWhereHas('sales', function ($query) use ($searchValue) {
                    $query->where('customer_id', $searchValue);
                });

            });
        }

        if(isset($from_date)) {
            $query->where(function($query) use ($from_date) {
                $query->whereDate('payment_collections.created_at', '>=', $from_date);
            });
        }

        if(isset($to_date)) {
            $query->where(function($query) use ($to_date) {
                $query->whereDate('payment_collections.created_at', '<=', $to_date);
            });
        }

        if(isset($customer_id)) {

            $query->where(function($query) use ($customer_id) {
                $query->orWhereHas('sales', function ($query) use ($customer_id) {
                    $query->where('sales.customer_id', $customer_id);
                });
            });
        }

        $sales_data = $query->paginate($length);
        $return_data = [
            'data' => $sales_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];
        return $this->sendResponse($return_data, 'Sales data retrieved successfully');
    }

    public function getProductSaleReport(Request $request)
    {
        $columns = ['id', 'product_id', 'customer_id', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");
        $product_id = request('product_id');
        $customer_id = request('customer_id');

        $query = SaleItem::with(['sales', 'products'])->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('sales', function ($query) use ($searchValue) {
                    $query->where('created_at', 'like', '%' .$searchValue. '%');
                });
                $query->orWhere('mrp_price', 'like', '%' .$searchValue. '%');
                $query->orWhereHas('sales', function ($query) use ($searchValue) {
                    $query->where('customer_name', 'like', '%' .$searchValue. '%');
                });

            });
        }

        if(isset($product_id)) {
            $query->where(function($query) use ($product_id) {
                $query->where('product_id', $product_id);
            });
        }

        if(isset($customer_id)) {
            $query->where(function($query) use ($customer_id) {
                $query->whereHas('sales', function ($query) use ($customer_id) {
                    $query->where('customer_id', '=', $customer_id);
                });
            });
        }

        if(isset($from_date)) {
            $query->where(function($query) use ($from_date) {
                $query->whereHas('sales', function ($query) use ($from_date) {
                    $query->whereDate('created_at', '>=', $from_date);
                });
            });
        }

        if(isset($to_date)) {
            $query->where(function($query) use ($to_date) {
                $query->whereHas('sales', function ($query) use ($to_date) {
                    $query->whereDate('created_at', '<=', $to_date);
                });
            });
        }

        $sales_data = $query->paginate($length);
        $return_data    = [
            'data' => $sales_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];
        return $this->sendResponse($return_data, 'Sales Data retrieved successfully');
    }

    public function excelExportDailySalesReport(Request $request){ 
       $response = $this->getDailySalesReport($request);
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data->data->data;  

        $customHeadings = [ ['Daily Sale Report'],[]];
        $columns = ['Sl', 'sales_date','sales_amount']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:C1';
        $margeRangeTwo = 'A2:C2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'Daily-Sale-Report.xlsx');
    } 

    public function excelExportCollection(Request $request){ 

         // Call the index method to retrieve the JSON response
         $response = $this->getSalesReport($request);  

         // Get the data array from the JSON response
         $data = $response->getData(); 
 
         // Now $data contains the array of data 
         $return_data = SalesReportResource::collection($data->data->data->data); 
 
         return $this->sendResponse($return_data,'');
    } 
    public function excelExportSalesReport(Request $request){ 
        $response = $this->excelExportCollection($request);
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data;  

        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $customHeadings = [ ['Sale Report'],['From '.$from_date.' TO '.$to_date]];
        $columns = ['Sl','data','reference_no','customer_name','total_amount','order_discount','paid_amount','due']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:H1';
        $margeRangeTwo = 'A2:H2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'Daily-Sale-Report.xlsx'); 
   
    } 
    public function productSalesExportCollection(Request $request){ 

         // Call the index method to retrieve the JSON response
         $response = $this->getProductSaleReport($request);  

         // Get the data array from the JSON response
         $data = $response->getData(); 
 
         // Now $data contains the array of data 
         $return_data = ProductsSalesReportResource::collection($data->data->data->data); 
 
         return $this->sendResponse($return_data,'');
    } 
    public function excelExportProductSalesReport(Request $request){ 
        $response = $this->productSalesExportCollection($request);
        // Get the data array from the JSON response
        $data = $response->getData(); 

        $returnData = $data->data;  

        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $customHeadings = [ ['Product Sale Report'],['From '.$from_date.' TO '.$to_date]];
        $columns = ['Sl','data', 'customer_name','product','price','qty','amount']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:H1';
        $margeRangeTwo = 'A2:H2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'Daily-Sale-Report.xlsx'); 
   
    } 
 
}
