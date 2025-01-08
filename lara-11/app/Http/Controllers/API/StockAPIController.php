<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Imports\StockProductsInOutImport;
use App\Models\StockProduct;
use App\Models\StockProductsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAPIController extends AppBaseController
{
    private $stProductObj;
    public function __construct()
    {
        $this->stProductObj = new StockProduct();
    }

    public function stockManagement(Request $request)
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

    public function stockManagementTest(Request $request)
    {
        $columns = ['name', 'bn_name', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = District::select('id', 'name', 'bn_name', 'division_id', 'status', 'created_at')->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('bn_name', 'like', '%' .$searchValue. '%');
            });
        }

        $districts = $query->paginate($length);
        $return_data    = [
            'data' => $districts,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Districts retrieved successfully');
    }

    // For Stock In/Out
    public function latestStockData(Request $request)
    { 
        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : array(); 
        if (in_array('Super Admin', $roles)) { 
            $outlet_id  = $request->input('outlet_id');
        }else{
            $outlet_id  = $request->input('outlet_id') ? $request->input('outlet_id') : $user->outlet_id;
        }   

        $columns = ['id','created_at', 'invoice_number', 'total_amount', 'customer_name', 'collection_amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = StockProduct::with(['product:id,product_name,product_code','outlet:id,name'])->orderBy('updated_at', 'DESC');
        if($outlet_id) {
            $query->where(function ($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id);
            });
        }
        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('product', function ($query) use ($searchValue) {
                    $query->where('products.product_name', 'like', '%' .$searchValue. '%'); 
                    $query->orWhere('products.product_code', 'like', '%' .$searchValue. '%'); 
                });
                $query->whereHas('outlet', function ($query) use ($searchValue) {
                    $query->orWhere('outlets.name', 'like', '%' .$searchValue. '%');  
                });
                $query->orWhere('in_stock_quantity', 'like', '%' .$searchValue. '%');
                $query->orWhere('stock_quantity', 'like', '%' .$searchValue. '%');
            });
        }

        $sales_data = $query->paginate($length); 

        // $return_data    = $stock_data->map(function ($item) {
        //     return [
        //         'id'    => $item->id,
        //         'product_id'    => $item->product_id,
        //         'outlet_id'     => $item->outlet_id,
        //         'outlet_name'   => $item->outlet->name ?? 'N/A',
        //         'product_name'  => $item->product->product_name,
        //         'product_code'  => $item->product->product_code,
        //         'expires_date'  => $item->expires_date ?? 'N/A',
        //         'in_stock_quantity' => $item->in_stock_quantity,
        //         'in_stock_weight' => $item->in_stock_weight,
        //         'out_stock_quantity'    => $item->out_stock_quantity,
        //         'out_stock_weight'    => $item->out_stock_weight,
        //         'stock_quantity'    => $item->stock_quantity,
        //         'stock_weight'    => $item->stock_weight,
        //     ];
        // });

        $return_data    = [
            'data' => $sales_data,
            'draw' => $request->input('draw')
        ];

        return $this->sendResponse($return_data, " sdsd Stock Data Retrieve Successfully!");
    }

    public function stockInOut(Request  $request)
    {
        $this->validate($request, [
            'outlet_id' => 'required',
            'product_id'=> 'required',
        ]);

        $outlet_id  = $request->get('outlet_id');
        $product_id = $request->get('product_id');
        $expires_date = $request->get('expires_date');
        $in_stock_quantity = $request->get('in_stock_quantity') ?? 0;
        $out_stock_quantity = $request->get('out_stock_quantity') ?? 0;
        $note   = $request->get('note');

        DB::beginTransaction();
        try{

            if(!empty($expires_date)) {
                $stock_data = StockProduct::where('outlet_id', $outlet_id)->where('product_id', $product_id)->whereDate('expires_date', $expires_date)->first();

                $stock_qty  = $stock_data->stock_quantity ?? 0;
                $stock_log_input    = [
                    'product_id' => $product_id,
                    'outlet_id' => $outlet_id,
                    'in_stock_quantity' => $in_stock_quantity,
                    'stock_quantity'    => (($stock_qty + $in_stock_quantity) - $out_stock_quantity),
                    'out_stock_quantity'    => $out_stock_quantity,
                    'expires_date'  => $expires_date,
                    'stock_type'    => 'IO',
                    'note'  => $note,
                    'user_id'   => auth()->user()->id ?? 1,
                ];
                if(empty($stock_data)) {
                    $insert_input = [
                        'product_id' => $product_id,
                        'outlet_id' => $outlet_id,
                        'in_stock_quantity' => $in_stock_quantity,
                        'stock_quantity'    => $in_stock_quantity - $out_stock_quantity,
                        'out_stock_quantity'    => $out_stock_quantity,
                        'expires_date'  => $expires_date,
                    ];

                    $stock_save = StockProduct::create($insert_input);
                }else{
                    $update_input = [
                        'in_stock_quantity' => ($stock_data->in_stock_quantity + $in_stock_quantity),
                        'stock_quantity'    => (($stock_data->stock_quantity + $in_stock_quantity) - $out_stock_quantity),
                        'out_stock_quantity'    => ($stock_data->out_stock_quantity + $out_stock_quantity),
                    ];

                    $stock_save = $stock_data->update($update_input);
                }

                $stock_log_save = StockProductsLog::create($stock_log_input);

            }
            // Stock Without Expires Date
            else{
                $stock_data = StockProduct::where('outlet_id', $outlet_id)->where('product_id', $product_id)->whereNull('expires_date')->first();

                $stock_qty = $stock_data->stock_quantity ?? 0;
                $stock_log_input    = [
                    'product_id' => $product_id,
                    'outlet_id' => $outlet_id,
                    'in_stock_quantity' => $in_stock_quantity,
                    'stock_quantity'    => (($stock_qty + $in_stock_quantity) - $out_stock_quantity),
                    'out_stock_quantity'    => $out_stock_quantity,
                    'stock_type'    => 'IO',
                    'note'  => $note,
                    'user_id' => auth()->user()->id ?? 0,
                ];

                if(empty($stock_data)) {
                    $insert_input = [
                        'product_id' => $product_id,
                        'outlet_id' => $outlet_id,
                        'in_stock_quantity' => $in_stock_quantity,
                        'stock_quantity'    => $in_stock_quantity - $out_stock_quantity,
                        'out_stock_quantity'    => $out_stock_quantity,
                    ];

                    $stock_save = StockProduct::create($insert_input);
                }else{
                    $update_input = [
                        'in_stock_quantity' => ($stock_data->in_stock_quantity + $in_stock_quantity),
                        'stock_quantity'    => (($stock_data->stock_quantity + $in_stock_quantity) - $out_stock_quantity),
                        'out_stock_quantity'    => ($stock_data->out_stock_quantity + $out_stock_quantity),
                    ];

                    $stock_save = $stock_data->update($update_input);
                }

                $stock_log_save = StockProductsLog::create($stock_log_input);
            }

            DB::commit();
            return $this->sendSuccess('Stock Update Successfully Done');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    public function stockBulkInOut(Request $request)
    {
        $this->validate($request, [
            'excel_file'    => 'required',
        ]);

        $file = $request->file('excel_file');

        $importObj  = new StockProductsInOutImport();
        $import = $importObj->import($file);

//        return response()->json($importObj->in_out_data);

        if($import){
            return $this->sendSuccess('Bulk Stock successfully done!');
        }else{
            return $this->sendError('Something went wrong, please try again!');
        }
    }


}
