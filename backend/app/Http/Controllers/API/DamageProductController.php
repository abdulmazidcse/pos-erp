<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\DamageProduct;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\StockProductsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamageProductController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columns = ['sl', 'outlets.outlet_name', 'products.product_name', 'product_code', 'expires_date', 'damage_quantity'];
        // name&category_id=3&sub_cat_id=35&brand_id=1
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $sortKey = $request->input('sortKey');

        $searchValue = $request->input('search');
        $outlet_id = $request->input('outlet_id');
        $product_id = $request->input('product_id');
        $from_date = $request->input('start_date');
        $to_date = $request->input('end_date');

        $query = DamageProduct::with(['outlets', 'products'])
            ->when($sortKey == "products.product_name", function($query) use($dir){
                return pleaseSortMe($query, $dir, Product::select('products.product_name')
                    ->whereColumn('products.id', 'damage_products.product_id')
                    ->take(1));
            })->when($sortKey == "outlets.name", function($query) use($dir){
                return pleaseSortMe($query, $dir, Outlet::select('outlets.name')
                    ->whereColumn('outlets.id', 'damage_products.outlet_id')
                    ->take(1));
            })
            ->when(!in_array($sortKey, ["outlets.name", "products.product_name"]), function($query) use($dir, $columns, $column){
                return $query->orderBy($columns[$column], $dir);
            });

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('product', function ($q) use ($searchValue) {
                    $q->where('product_name', 'like', '%'.$searchValue.'%');
                    $q->orWhere('product_code', 'like', '%'.$searchValue.'%');
                });
                $query->orWhere('damage_quantity', 'like', '%' .$searchValue. '%');
                $query->orWhere('expires_date', 'like', '%' .$searchValue. '%');
            });
        }
        if($outlet_id) {
            $query->where(function ($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id );
            });
        }
        if($product_id) {
            $query->where(function ($query) use ($product_id) {
                $query->where('product_id',$product_id);
            });
        }

        if(isset($from_date)) {
            $query->whereDate('created_at', '>=', $from_date);
        }

        if(isset($to_date)) {
            $query->whereDate('created_at', '<=', $to_date);
        }

        $damage_products = $query->paginate($length);
        $return_data    = [
            'data' => $damage_products,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Damage products retrieved successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'outlet_id' => 'required',
            'product_id'    => 'required',
            'damage_quantity'   => 'required'
        ]);

        $inputs = $request->all();

        $outlet_id  = $request->get('outlet_id');
        $product_id = $request->get('product_id');
        $damage_quantity    = $request->get('damage_quantity');
        $expires_date   = "";

        $query  = StockProduct::where('outlet_id', $outlet_id)
                    ->where('product_id', $product_id);

        if($request->get('expires_date') != "") {
            $expires_date   = date("Y-m-d", strtotime($request->get('expires_date')));
            $query->where('expires_date', $expires_date);
        }

        $stock_product  = $query->first();

        if(empty($stock_product)) {
            return $this->sendError('This product not available in stock');
        }

        if($stock_product->stock_quantity < $damage_quantity) {
            return $this->sendError("Damage quantity can't be getter then stock quantity");
        }

        $product    = Product::find($product_id);
        $damage_inputs  = [
            'outlet_id' => $outlet_id,
            'product_id'    => $product_id,
            'product_stock_id'  => $stock_product->id,
            'expires_date'  => $expires_date ? $expires_date : NULL,
            'damage_quantity'   => $damage_quantity,
            'cost_price'    => $product->cost_price,
            'notes' => $request->get('notes')
        ];

        $update_stock_quantity   = $stock_product->stock_quantity - $damage_quantity;
        $update_out_stock_quantity  = $stock_product->out_stock_quantity + $damage_quantity;

        $stock_update_input   = [
            'stock_quantity'    => $update_stock_quantity,
            'out_stock_quantity'    => $update_out_stock_quantity
        ];

        $stock_log_inputs    = [
            'product_id' => $product_id,
            'outlet_id' => $outlet_id,
            'in_stock_quantity' => 0,
            'stock_quantity'    => $update_stock_quantity,
            'out_stock_quantity'    => $damage_quantity,
            'expires_date'  => $expires_date ?? NULL,
            'stock_type'    => 'DP',
            'note'  => $request->get('notes'),
            'user_id'   => auth()->user()->id ?? 1,
        ];

        DB::beginTransaction();
        try {
            $dp_create  = DamageProduct::create($damage_inputs);
            $stock_update   = $stock_product->update($stock_update_input);
            $stock_log_save = StockProductsLog::create($stock_log_inputs);


            DB::commit();
            return $this->sendSuccess("Damage product add successfully done!");

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
