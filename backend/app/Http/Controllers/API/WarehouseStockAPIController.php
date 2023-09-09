<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\WarehouseStockProduct;
use Illuminate\Http\Request;

class WarehouseStockAPIController extends AppBaseController
{
    private $stProductObj;
    public function __construct()
    {
        $this->stProductObj = new WarehouseStockProduct();
    }

    public function warehouseStockManagement(Request $request)
    {
//        $stock_data = new StockProduct();
////        ->filtered($request->all())
//        $stock_data->all();


        $length = $request->input('length') ?? 10;
        $stock_data = $this->stProductObj->filtered()->paginate($length);
//        $stock_data = StockProduct::orderBy('id', 'DESC')->get();

//        $return_data    = $stock_data->map(function ($item) {
//            return [
//                'id'    => $item->id,
//                'product_id'    => $item->product_id,
//                'outlet_id' => $item->outlet_id,
//                'outlet_name'   => $item->outlet->name,
//                'product_name'  => $item->product->product_name,
//                'product_code'  => $item->product->product_code,
//                'expires_date'  => $item->expires_date ?? 'N/A',
//                'in_stock_quantity' => $item->in_stock_quantity,
//                'out_stock_quantity'    => $item->out_stock_quantity,
//                'stock_quantity'    => $item->stock_quantity,
//            ];
//        });

        return $this->sendResponse($stock_data, "Stock Data Retrieve Successfully!");
    }
}
