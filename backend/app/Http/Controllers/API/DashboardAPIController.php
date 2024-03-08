<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\PaymentCollection;
use App\Models\SaleItem;
use App\Models\StockProduct;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAPIController extends AppBaseController
{
    private $stProductObj;
    public function __construct()
    {
        $this->stProductObj = new StockProduct();
    }

    public function dashboard(Request $request)
    {
        $customer_query = Customer::where('status', 1);
        $order_query    = Sale::orderBy('id', 'desc');
        $supplier_query = Supplier::where('status', 1);
        $product_query  = Product::where('status', 1);
        $annualReport = self::annualReport($request);
        $topSalesProducts = self::topProducts($request);
        $salesVsPurchases = self::salesVsPurchases($request);


        $return_data = [
            'customer_count'  => $customer_query->count(),
            'order_count' => $order_query->count(),
            'supplier_count'  => $supplier_query->count(),
            'product_count'   => $product_query->count(),
            'annual_sales_report' => $annualReport,
            'top_sales_products' => $topSalesProducts,
            'sales_vs_purchases' => $salesVsPurchases,
        ];

        return $this->sendResponse($return_data, 'Data Retrieve Successfully');
    }

    public function dashboardProductStock(Request $request)
    {

        // Product Stock Report
        $length = $request->input('length') ?? 10;
        $stock_data = $this->stProductObj->filtered()->paginate($length);

        $return_data    = [
            'stock_data'      => $stock_data,
        ];

        return $this->sendResponse($return_data, 'Data Retrieve Successfully');

    }


    public function dashboardProductSaleReport(Request $request)
    {
        $columns = ['id', 'created_at', 'product_id', 'item_quantity', 'mrp_price'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
//        $from_date = request('from_date') ?? Carbon::now()->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $query = SaleItem::with(['products'])
            ->selectRaw('*, sum(quantity) as item_quantity, sum(weight) as item_weight')
            ->groupBy(['product_id', DB::raw('DATE(created_at)'), 'mrp_price'])
            ->orderBy($columns[$column], $dir);

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
    public function dashboardSaleReport(Request $request)
    { 

        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d"); 
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $query = SaleItem::with(['products'])
            ->selectRaw('*, sum(quantity) as item_quantity, sum(weight) as item_weight')
            ->groupBy(['product_id', DB::raw('DATE(created_at)'), 'mrp_price']);
 

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

        $sales_data = $query->get();
        $return_data    = [
            'data' => $sales_data,
            'draw' => $request->input('draw'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
        ];
        return $this->sendResponse($return_data, 'Sales Data retrieved successfully');
    }

    public function invoiceWiseProfit(Request $request){
        $invoiceWise = SaleItem::select(  
            'sale_id',
            'invoice_number',
            DB::raw('SUM(discount * quantity) AS tdis'),
            DB::raw('SUM(mrp_price * quantity) AS tprice'),
            DB::raw('SUM(cost_price * quantity) AS tcprice'),
            DB::raw('SUM(mrp_price * quantity - cost_price * quantity - discount * quantity) AS profit')
        )
        ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
        ->groupBy('sale_id')
        ->get();
        return $this->sendResponse($invoiceWise, 'Sales Data retrieved successfully');
    }

    public function annualReport(Request $request)
    { 
        $year =  $request->get('year') ?? '2023';//date('Y');
        $annualSales = SaleItem::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(discount * quantity) AS total_discount'),
            DB::raw('SUM(mrp_price * quantity) AS total_price'),
            DB::raw('SUM(cost_price * quantity) AS total_cost_price'),
            DB::raw('SUM(mrp_price * quantity - cost_price * quantity - discount * quantity) AS total_profit')
        )
        ->where(DB::raw('YEAR(created_at)'), $year ) 
        ->first();  

        $annualSum = Sale::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(order_discount) AS total_order_discount'),
            DB::raw('SUM(total_amount) AS total_total_amount'),
            DB::raw('SUM(grand_total) AS total_grand_total')
        )
        ->where(DB::raw('YEAR(created_at)'), $year) 
        ->first(); 

        $annualCollections = PaymentCollection::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(amount) AS total_amount'), 
        )
        ->where(DB::raw('YEAR(created_at)'), $year) 
        ->groupBy('paying_by') 
        ->first();  

        return $data = [
            'annual_sale_item' => $annualSales,
            'annual_sales' => $annualSum, 
            'annual_collection' => $annualCollections, 
        ];
        // return $this->sendResponse($data, 'Sales Data retrieved successfully'); 
    }

    public function topProducts(Request $request)
    {
        $year =  $request->get('year') ?? '2023';//date('Y'); 
        $topProducts = Product::active()
            ->select('id', 'product_name')
            ->withCount(['salesItems' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])
            ->orderByDesc('sales_items_count')
            ->take(5)
            ->get();
    

        return ['topProducts' => $topProducts];
    }

    public function monthWiseSalesReport(Request $request){
        // Retrieve sales data grouped by month
        $salesData = Sale::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total_sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format the data for chart
        $formattedData = [];
        foreach ($salesData as $item) {
            $formattedData['months'][] = Carbon::createFromFormat('!m', $item->month)->format('M');
            $formattedData['sales'][] = $item->total_sales;
        } 
        return $formattedData;
    }
    public function monthWisePurchasesReport(Request $request){
        // Retrieve sales data grouped by month
        $salesData = PurchaseReceive::selectRaw('MONTH(created_at) as month, SUM(net_amount) as total_purchase')
            ->groupBy('month')
            ->orderBy('month')
            ->get(); 
        $formattedData = [];
        foreach ($salesData as $item) {
            $formattedData['months'][] = Carbon::createFromFormat('!m', $item->month)->format('M');
            $formattedData['purchase'][] = $item->total_purchase;
        }
        return $formattedData;
    }

    public function salesVsPurchases(Request $request){
        $salesData = self::monthWiseSalesReport($request);       
        $purchases = self::monthWisePurchasesReport($request);       
          
        $salesDataCombine = array_combine($salesData['months'], $salesData['sales']); 
        $purchaseDataCombine = array_combine($purchases['months'], $purchases['purchase']);   
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $sales_data = [0,0,0,0,0,0,0,0,0,0,0,0];
        $purchases_data = [0,0,0,0,0,0,0,0,0,0,0,0];
        foreach ($months as $key => $value) {  
            foreach ($salesDataCombine as $key2 => $value2) {
                if($value==$key2){
                    $sales_data[$key] = $value2;
                }  
            }  
        } 
        foreach ($months as $key => $value) {  
            foreach ($purchaseDataCombine as $key2 => $value2) {
                if($value==$key2){
                    // $formattedValue =  number_format($value2, 2, '.', ','); 
                    // $purchases_data[$key] = str_replace('"', '', $formattedValue);  
                    $purchases_data[$key] = $value2;  
                }  
            }  
        } 
        
        return [
            "months" => $months, // Assuming months are the same in both arrays
            "sales" => $sales_data, // Initialize with zeros
            "purchase" => $purchases_data, // Initialize with zeros
        ];           
    }
}
