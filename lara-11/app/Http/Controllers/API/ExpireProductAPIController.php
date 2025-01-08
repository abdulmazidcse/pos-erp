<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpireProductAPIController extends AppBaseController
{
    public function expiryBoard()
    {
        $select_query = "SELECT stock_products.id, products.product_name, outlets.name as outlet_name, stock_products.stock_quantity, stock_products.expires_date, t1.available_days, products.return_policy
FROM stock_products
LEFT JOIN products ON stock_products.`product_id` = products.`id`
LEFT JOIN outlets ON stock_products.`outlet_id` = outlets.`id`
JOIN (SELECT id, DATEDIFF(expires_date, CURDATE()) AS available_days FROM stock_products) AS t1 ON
stock_products.id = t1.id
JOIN (SELECT id, (return_policy + 7) AS total_day FROM products) AS t2 ON
stock_products.product_id = t2.id
WHERE stock_products.expires_date IS NOT NULL
AND t1.available_days >= t2.total_day";

        $expire_stock_products = DB::select(DB::raw($select_query));

//        $expire_stock_products = DB::table('stock_products')
//                                    ->select('stock_products.*', 'products.product_name', 'products.product_code', 'outlets.name', 't1.*', 't2.*','t1.available_days')
//                                    ->leftJoin('products', 'stock_products.product_id', '=', 'products.id')
//                                    ->leftJoin('outlets', 'stock_products.outlet_id', '=', 'outlets.id')
//                                    ->join(DB::raw("(SELECT id, DATEDIFF(expires_date, CURDATE()) AS available_days FROM stock_products) AS t1"), 'stock_products.id', '=', 't1.id')
//                                    ->join(DB::raw("(SELECT id, (return_policy + 7) AS total_day FROM products) AS t2"), 'stock_products.product_id', '=', 't2.id')
//                                    ->whereNotNull('stock_products.expires_date')
//                                    ->where('t1.available_days', '>=', 't2.total_day')
//                                    ->get()->toArray();
//
//        return array_filter(array_map(function($value){
//            return $value->available_days >= $value->total_day ? $value : '';
//        }, $expire_stock_products));


        return $this->sendResponse($expire_stock_products, 'Expired Data Retrieved Successfully');
    }
}
