<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockProduct;
use App\Models\Supplier;
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



        $return_data    = [
            'customer_count'  => $customer_query->count(),
            'order_count' => $order_query->count(),
            'supplier_count'  => $supplier_query->count(),
            'product_count'   => $product_query->count(),
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
}
