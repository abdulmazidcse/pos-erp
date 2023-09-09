<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\StockProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryManagementController extends AppBaseController
{
    public function inventoryBoard(Request $request)
    {
        $current_date = Carbon::now()->format('Y-m-d');

        if($request->from_date != '' && $request->to_date != '') {
            $from_date = $request->get('from_date');
            $to_date    = $request->get('to_date');
        }else{
            $from_date = Carbon::now()->subDays(30)->format("Y-m-d");
            $to_date    = $current_date;
        }

        $where = "WHERE (DATE(sp.expires_date) > CURRENT_DATE OR sp.expires_date IS NULL)";
//        $where = "";

        if($request->get('outlet_id') != '') {
            if($where == "") {
                $where .= "WHERE sp.outlet_id = {$request->get('outlet_id')}";
            }else{
                $where .= " AND sp.outlet_id = {$request->get('outlet_id')}";
            }
        }

//        $from_date = Carbon::now()->subDays(30)->format("Y-m-d");
//        $to_date    = $current_date;

        $sql_query = "SELECT sp.product_id, p.product_name, sp.outlet_id, o.name as outlet_name, sp.expires_date, SUM(sp.stock_quantity) as stock_quantity, COALESCE(st.sale_quantity,0) as sale_quantity, COALESCE(st.total_days,0) as total_days, 
COALESCE(ROUND(st.sale_quantity/st.total_days,0),0) as average_sale_quantity, ord.max_order_date, ord.order_quantity, ord.order_date 
FROM stock_products sp 
JOIN products p 
ON p.id = sp.product_id
JOIN outlets o
ON o.id = sp.outlet_id
LEFT JOIN (SELECT product_id, SUM(quantity) as sale_quantity, DATEDIFF('{$to_date}', '{$from_date}') as total_days FROM sale_items 
      WHERE DATE(created_at) BETWEEN '{$from_date}' AND '{$to_date}'
      GROUP BY product_id) as st 
ON st.product_id = sp.product_id
LEFT JOIN (SELECT a.product_id, a.order_quantity, b.max_order_date, DATE(a.created_at) as order_date FROM order_requisition_details a
			INNER JOIN (SELECT `product_id`, max(`created_at`) as max_order_date FROM `order_requisition_details`
			group by product_id) b 
			ON b.product_id = a.product_id 
			AND a.created_at = b.max_order_date
			ORDER BY a.product_id ASC) as ord
ON ord.product_id = sp.product_id {$where} GROUP BY sp.product_id, sp.outlet_id
ORDER BY average_sale_quantity DESC, sp.stock_quantity ASC";

//        return $sql_query;
        $inventory_data = DB::select(DB::raw($sql_query));

        return $this->sendResponse($inventory_data, 'Data Retrieve Successfully');
    }
}
