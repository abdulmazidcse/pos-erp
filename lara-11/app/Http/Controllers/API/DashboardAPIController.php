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
        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : array(); 

        if (in_array('Super Admin', $roles)) { 
            $outlet_id  = $request->input('outlet_id');
        }else{
            $outlet_id  = $request->input('outlet_id') ? $request->input('outlet_id') : $user->outlet_id;
        } 

        $customer_query = Customer::where('status', 1);
        $order_query    = Sale::orderBy('id', 'desc');
        $supplier_query = Supplier::where('status', 1);
        $product_query  = Product::where('status', 1);

        $annualReport = self::annualReport($request);
        $topSalesProducts = self::topProducts($request);
        $salesVsPurchases = self::salesVsPurchases($request);
 
        $toDay = Carbon::today(); 
        $yesterday = Carbon::yesterday(); 
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SATURDAY); 
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::FRIDAY); 
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::SATURDAY); 
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek(Carbon::FRIDAY); 
        $currentMonth = Carbon::now()->month; 

        $startOfMonth = Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->toDateTimeString();
        $endOfMonth = Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->toDateTimeString();

        $currentYear = Carbon::now()->year;
        $todaySalesAmount = Sale::whereDate('created_at', $toDay)
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');

        $yesterdaySalesAmount = Sale::whereDate('created_at', $yesterday)
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');

        $currentWeekSalesAmount = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');

        $previousWeekSalesAmount = Sale::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');

        $currentMonthSalesAmount = Sale::whereMonth('created_at', $currentMonth)
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');

        $previousMonthSalesAmount = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');
            
        $currentYearSalesAmount = Sale::whereYear('created_at', $currentYear)
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })->sum('grand_total');
            
        $return_data    = [
            'customer_count'  => $customer_query->count(),
            'order_count' => $order_query->count(),
            'supplier_count'  => $supplier_query->count(),
            'product_count'   => $product_query->count(),
            'annual_sales_report' => $annualReport,
            'top_sales_products' => $topSalesProducts,
            'sales_vs_purchases' => $salesVsPurchases,
   

            'todaySalesAmount'  => number_format((float)$todaySalesAmount, 2, '.', ','),
            'yesterdaySalesAmount'  => number_format((float)$yesterdaySalesAmount, 2, '.', ','),
            'currentWeekSalesAmount'  => number_format((float)$currentWeekSalesAmount, 2, '.', ','), 
            'previousWeekSalesAmount'  => number_format((float)$previousWeekSalesAmount, 2, '.', ','),             
            'currentMonthSalesAmount'  => number_format((float)$currentMonthSalesAmount, 2, '.', ','),
            'previousMonthSalesAmount'  => number_format((float)$previousMonthSalesAmount, 2, '.', ','),
            'currentYearSalesAmount'   => number_format((float)$currentYearSalesAmount, 2, '.', ','), 
            'last7DaysSales'   => self::getLast7DaysSales($request)->original, 
        ];


        return $this->sendResponse($return_data, 'Data Retrieve Successfully');
    }
    public function getLast7DaysSales(Request $request)
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(7);

        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : array(); 

        if (in_array('Super Admin', $roles)) { 
            $outlet_id  = $request->input('outlet_id');
        }else{
            $outlet_id  = $request->input('outlet_id') ? $request->input('outlet_id') : $user->outlet_id;
        } 

        // Fetch sales data for the last 7 days including dates with zero sales
        $salesData = DB::table('sales')
            ->rightJoin(DB::raw("(SELECT DATE_SUB(CURDATE(), INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY) AS date
                        FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                        ) dates"), function($join) {
                $join->on(DB::raw("DATE(sales.created_at)"), '=', 'dates.date');
            })
            ->select(DB::raw('dates.date'), DB::raw('COALESCE(SUM(sales.grand_total), 0) as total_sales'))
            ->whereBetween('dates.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->when($outlet_id, function ($q, $outlet_id) {
                return $q->where('outlet_id', $outlet_id);
            })
            ->groupBy('dates.date')
            ->orderBy('dates.date', 'asc')
            ->get();

        // Transform the sales data to include zero sales for missing dates
        $original = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $found = false;
            foreach ($salesData as $sales) {
                if ($sales->date == $currentDate->format('Y-m-d')) {
                    $original[] = ['date' => $sales->date, 'total_sales' => $sales->total_sales];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $original[] = ['date' => $currentDate->format('Y-m-d'), 'total_sales' => 0];
            }
            $currentDate->addDay();
        }

        return response()->json($original);
    }

    public function dashboardBackup(Request $request)
    {
        $customer_query = Customer::where('status', 1);
        $order_query    = Sale::orderBy('id', 'desc');
        $supplier_query = Supplier::where('status', 1);
        $product_query  = Product::where('status', 1);

        $annualReport = self::annualReport($request);
        $topSalesProducts = self::topProducts($request);
        $salesVsPurchases = self::salesVsPurchases($request);
 
        $toDay = Carbon::today(); 
        $yesterday = Carbon::yesterday(); 
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SATURDAY); 
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::FRIDAY); 
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::SATURDAY); 
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek(Carbon::FRIDAY); 
        $currentMonth = Carbon::now()->month; 

        $startOfMonth = Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->toDateTimeString();
        $endOfMonth = Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->toDateTimeString();

        $currentYear = Carbon::now()->year;
        $todaySalesAmount = Sale::whereDate('created_at', $toDay)->sum('grand_total');
        $yesterdaySalesAmount = Sale::whereDate('created_at', $yesterday)->sum('grand_total');
        $currentWeekSalesAmount = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('grand_total');
        $previousWeekSalesAmount = Sale::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->sum('grand_total');
        $currentMonthSalesAmount = Sale::whereMonth('created_at', $currentMonth)->sum('grand_total');
        $previousMonthSalesAmount = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('grand_total');
        $currentYearSalesAmount = Sale::whereYear('created_at', $currentYear)->sum('grand_total');


        $return_data    = [
            'customer_count'  => $customer_query->count(),
            'order_count' => $order_query->count(),
            'supplier_count'  => $supplier_query->count(),
            'product_count'   => $product_query->count(),
            'annual_sales_report' => $annualReport,
            'top_sales_products' => $topSalesProducts,
            'sales_vs_purchases' => $salesVsPurchases,
   

            'todaySalesAmount'  => number_format((float)$todaySalesAmount, 2, '.', ','),
            'yesterdaySalesAmount'  => number_format((float)$yesterdaySalesAmount, 2, '.', ','),
            'currentWeekSalesAmount'  => number_format((float)$currentWeekSalesAmount, 2, '.', ','), 
            'previousWeekSalesAmount'  => number_format((float)$previousWeekSalesAmount, 2, '.', ','),             
            'currentMonthSalesAmount'  => number_format((float)$currentMonthSalesAmount, 2, '.', ','),
            'previousMonthSalesAmount'  => number_format((float)$previousMonthSalesAmount, 2, '.', ','),
            'currentYearSalesAmount'   => number_format((float)$currentYearSalesAmount, 2, '.', ','), 
            'last7DaysSales'   => self::getLast7DaysSales()->original, 
        ];


        return $this->sendResponse($return_data, 'Data Retrieve Successfully');
    }

    public function getLast7DaysSalesBack()
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(7);

        // Fetch sales data for the last 7 days including dates with zero sales
        $salesData = DB::table('sales')
            ->rightJoin(DB::raw("(SELECT DATE_SUB(CURDATE(), INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY) AS date
                        FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                        ) dates"), function($join) {
                $join->on(DB::raw("DATE(sales.created_at)"), '=', 'dates.date');
            })
            ->select(DB::raw('dates.date'), DB::raw('COALESCE(SUM(sales.grand_total), 0) as total_sales'))
            ->whereBetween('dates.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('dates.date')
            ->orderBy('dates.date', 'asc')
            ->get();

        // Transform the sales data to include zero sales for missing dates
        $original = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $found = false;
            foreach ($salesData as $sales) {
                if ($sales->date == $currentDate->format('Y-m-d')) {
                    $original[] = ['date' => $sales->date, 'total_sales' => $sales->total_sales];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $original[] = ['date' => $currentDate->format('Y-m-d'), 'total_sales' => 0];
            }
            $currentDate->addDay();
        }

        return response()->json($original);
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
    public function monthWisePurchasesReportAddfsf(Request $request){
        // Retrieve sales data grouped by month
        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : []; 
    
        if (in_array('Super Admin', $roles)) { 
            $outlet_id = $request->input('outlet_id');
        } else {
            $outlet_id = $request->input('outlet_id') ? $request->input('outlet_id') : $user->outlet_id;
        } 
    
        // Start building the query
        $query = PurchaseReceive::selectRaw('MONTH(created_at) as month, SUM(net_amount) as total_purchase')
            ->groupBy('month')
            ->orderBy('month');
    
        // Filter by outlet_id if it's provided
        if ($outlet_id) {
            $query->where('outlet_id', $outlet_id);
        }
    
        // Execute the query
        $salesData = $query->get(); 
    
        // Initialize the formatted data array
        $formattedData = ['months' => [], 'purchase' => []];
    
        // Process the data
        foreach ($salesData as $item) {
            $formattedData['months'][] = Carbon::createFromFormat('!m', $item->month)->format('M');
            $formattedData['purchase'][] = $item->total_purchase;
        }
    
        return $formattedData;
    }

    public function monthWisePurchasesReport(Request $request){
        // Retrieve sales data grouped by month
        $salesData = PurchaseReceive::selectRaw('MONTH(created_at) as month, SUM(net_amount) as total_purchase')
            ->groupBy('month')
            ->orderBy('month')
            ->get(); 
            // dd( $salesData->toArray());
        $formattedData = []; 
        if($salesData){
            foreach ($salesData as $item) {
                $formattedData['months'][] = Carbon::createFromFormat('!m', $item->month)->format('M');
                $formattedData['purchase'][] = $item->total_purchase;
            }
        }
        return $formattedData;
    }

    public function salesVsPurchases(Request $request) {
        // Fetch data from reports
        $salesData = self::monthWiseSalesReport($request);       
        $purchases = self::monthWisePurchasesReport($request);        
    
        // Check if data structure is correct
        if (!isset($salesData['months']) || !isset($salesData['sales'])) {
            return ['error' => 'Sales data structure is incorrect.'];
        }
        if (!isset($purchases['months']) || !isset($purchases['purchase'])) {
            return ['error' => 'Purchases data structure is incorrect.'];
        }
    
        // Combine data by months
        $salesDataCombine = array_combine($salesData['months'], $salesData['sales']); 
        $purchaseDataCombine = array_combine($purchases['months'], $purchases['purchase']);   
        
        // Define months and initialize data arrays
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $sales_data = array_fill(0, 12, 0);
        $purchases_data = array_fill(0, 12, 0);
        
        // Fill sales_data based on salesDataCombine
        foreach ($months as $key => $month) {  
            if (isset($salesDataCombine[$month])) {
                $sales_data[$key] = $salesDataCombine[$month];
            }  
        }
    
        // Fill purchases_data based on purchaseDataCombine
        foreach ($months as $key => $month) {  
            if (isset($purchaseDataCombine[$month])) {
                $purchases_data[$key] = $purchaseDataCombine[$month];  
            }  
        }
        
        return [
            "months" => $months,
            "sales" => $sales_data,
            "purchase" => $purchases_data,
        ];           
    }

}
