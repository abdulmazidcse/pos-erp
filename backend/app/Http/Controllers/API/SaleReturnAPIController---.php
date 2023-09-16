<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSaleReturnAPIRequest;
use App\Http\Requests\API\UpdateSaleReturnAPIRequest;
use App\Http\Resources\PosProductResource;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\SaleReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturnItem;
use App\Models\Product;
use App\Models\StockProduct;
use App\Repositories\SaleReturnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class SaleReturnController
 * @package App\Http\Controllers\API
 */

class SaleReturnAPIController extends AppBaseController
{
    /** @var  SaleReturnRepository */
    private $saleReturnRepository;

    public function __construct(SaleReturnRepository $saleReturnRepo)
    {
        $this->saleReturnRepository = $saleReturnRepo;
    }

    /**
     * Display a listing of the SaleReturn.
     * GET|HEAD /saleReturns
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $saleReturns = $this->saleReturnRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($saleReturns->toArray(), 'Sale Returns retrieved successfully');
    }

    public function list(Request $request)
    { 

        $columns = ['id','created_at', 'return_amount', 'return_reason', 'return_type'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = SaleReturn::with(['sale' => function($query){
            $query->select('id', 'invoice_number');
        }])->withCount(['saleReturnItems AS return_item_qty' => function($query){
            $query->select(\DB::raw("SUM(sale_r_qty) as sale_r_qty"));
        }])
        ->withCount('saleReturnItems AS return_item')->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('return_amount', 'like', '%' .$searchValue. '%');
                $query->orWhere('return_type', 'like', '%' .$searchValue. '%');  
            });
        }

        $data = $query->paginate($length); 
        $return_data    = [
            'data' => $data,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Hold Sale retrieved successfully');
    }

    /**
     * Store a newly created SaleReturn in storage.
     * POST /saleReturns
     *
     * @param CreateSaleReturnAPIRequest $request
     *
     * @return Response
     */
    public function storePrevious(CreateSaleReturnAPIRequest $request)
    {
        $input = $request->all();

        $input['sale_return_info']['return_type'] = 1;
        $input['sale_return_info']['sale_id'] = $input['returnInfo']['id'];

        $saleReturn = $this->saleReturnRepository->create($input['sale_return_info']);

        $model = Sale::findOrFail($input['returnInfo']['id']);
        $saleUpdate = array('return_type'=>1);
        $model = $model->fill($saleUpdate);
        $model->save();


        SaleItem::where('sale_id', '=', $input['returnInfo']['id'])
        ->update(['return_type' => 1]);


        $saleItem = SaleItem::where('sale_id', '=', $input['returnInfo']['id'])->get();
        if($input['return_replace']){
            foreach ($input['return_replace'] as $key => $value) {
                if($value['return_or_replace']){
                    if($value['return_or_replace'] == 0){
                        $return_or_replace = 1; // return
                        $model = SaleItem::findOrFail($value['item_id']);
                        //0=default 1=return 2=replace 3=void
                        $saleUpdate = array('return_type'=>1);
                        $model = $model->fill($saleUpdate);
                        $model->save();
                    } else {
                        $return_or_replace = 2; // replace
                        $product = Product::find( $value['replace_pro_id']);
                        $model = SaleItem::findOrFail($value['item_id']);
                        //0=default 1=return 2=replace 3=void
                        $saleUpdate = array(
                            'return_type'=> $return_or_replace, // Replace
                            'product_id' => $product->id,
                            'mrp_price'  => $product->mrp_price,
                            'cost_price' => $product->cost_price, 
                            'vat_id'     => $product->tax_method,
                        );
                        $model = $model->fill($saleUpdate);
                        $model->save();
                    }
                    if($value['return_qty'] > $value['item_qty']){
                        $return_qty = $value['item_qty'];
                    } else{
                        $return_qty = $value['return_qty'];
                    }
                    //0=default 1=return 2=replace 3=void
                    $items[] = new SaleReturnItem([
                        'sale_id'       => $input['returnInfo']['id'],
                        'sale_item_id'  => $value['item_id'],
                        'item_pro_id'   => $value['pro_id'],
                        'replace_pro_id'=> ($return_or_replace ==2) ? $value['replace_pro_id'] : '',
                        'sale_item_qty' => $value['item_qty'], 
                        'sale_r_qty'    => ($return_or_replace ==2) ? $value['item_qty'] : $return_qty, 
                        'return_type'   => $return_or_replace, 
                    ]);
                    $saleReturn->saleReturnItems()->saveMany($items);
                }            
            }
        } 

        return $this->sendResponse($input['return_replace'], 'Sale Return & Replace successfully');
    }

    // For Sales Return Specific Product
    public function store(CreateSaleReturnAPIRequest $request)
    {
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }
        $input = $request->all();

        return "specific returns==== ".$input;


        $sale_items = $input['returnInfo']['sales_items'];
        $sale_item_array = [];
        foreach ($sale_items as $sale_item) {
            $sale_item  = (object) $sale_item;
            $sale_item_array[$sale_item->id] = $sale_item;
        }

//        return $this->sendResponse([$input, $sale_item_array], 'Sale Return & Replace test');

        // Sale Return Item
        $total_item_discount = 0;
        $total_discount_amount = 0;
        $total_item_vat = 0;
        $total_item_mrp_amount = 0;
        $total_item_cost_amount = 0;
        $total_return_amount = 0;

        if($input['return_replace']){
            DB::beginTransaction();
            try {
                foreach ($input['return_replace'] as $key => $value) {
                    if($value['return_or_replace'] == 1){

                        $sitem  = $sale_item_array[$value['item_id']];
                        if($value['return_qty'] >= $value['item_qty']){
                            $return_qty = $value['item_qty'];
                        } else{
                            $return_qty = $value['return_qty'];
                        }
                        $item_discount = $sitem->discount * $return_qty;
                        $item_vat   = $sitem->vat * $return_qty;
                        $item_mrp_amount = $sitem->mrp_price * $return_qty;
                        $item_cost_amount = $sitem->cost_price * $return_qty;

                        $total_item_discount    += $item_discount;
                        $total_item_vat += $item_vat;
                        $total_item_mrp_amount  += $item_mrp_amount;
                        $total_item_cost_amount += $item_cost_amount;

//                        if($value['return_or_replace'] == 0){
                            $return_or_replace = 1; // return
                            $model = SaleItem::findOrFail($value['item_id']);
                            //0=default 1=return 2=replace 3=void
                            $saleUpdate = array('return_type'=>1);
                            $model = $model->fill($saleUpdate);
                            $model->save();
//                        }
//                        else {
//                            $return_or_replace = 2; // replace
//                            $product = Product::find( $value['replace_pro_id']);
//                            $model = SaleItem::findOrFail($value['item_id']);
//                            //0=default 1=return 2=replace 3=void
//                            $saleUpdate = array(
//                                'return_type'=> $return_or_replace, // Replace
//                                'product_id' => $product->id,
//                                'mrp_price'  => $product->mrp_price,
//                                'cost_price' => $product->cost_price,
//                                'vat_id'     => $product->tax_method,
//                            );
//                            $model = $model->fill($saleUpdate);
//                            $model->save();
//                        }


                        // Stock Update
                        $stock_data = StockProduct::where('id', $sitem->stock_id)->first();
                        $old_stock_quantity = $stock_data->stock_quantity;
                        $new_stock_quantity = $old_stock_quantity + $return_qty;
                        $stock_data->update(['stock_quantity' => $new_stock_quantity]);

                        //0=default 1=return 2=replace 3=void
                        $items[] = new SaleReturnItem([
                            'sale_id'       => $input['returnInfo']['id'],
                            'sale_item_id'  => $value['item_id'],
                            'item_pro_id'   => $value['pro_id'],
                            'replace_pro_id'=> ($return_or_replace == 2) ? $value['replace_pro_id'] : '',
                            'sale_item_qty' => $value['item_qty'],
                            'sale_r_qty'    => ($return_or_replace == 2) ? $value['item_qty'] : $return_qty,
                            'return_type'   => $return_or_replace,
                        ]);
                    }else{
                        $items = [];
                    }
                }

                $total_discount_amount  = $total_item_discount;

                $sales_return_transaction_data = [
                    'fiscal_year_id'    => $fiscal_year->id,
                    'total_discount_amount'    => $total_discount_amount,
                    'total_vat_amount'  => $total_item_vat,
                    'total_item_mrp_amount' => $total_item_mrp_amount,
                    'total_cogs_amount'    => $total_item_cost_amount,
//                    'total_cash_amount' => (($total_item_mrp_amount + $total_item_vat) - $total_discount_amount),
                    'total_cash_amount' => ($total_item_mrp_amount - $total_discount_amount),
                ];


                // For Sale return
                $input['sale_return_info']['return_type'] = 1;
                $input['sale_return_info']['sale_id'] = $input['returnInfo']['id'];
                $input['sale_return_info']['return_amount'] = $sales_return_transaction_data['total_cash_amount'];

                if(count($items) > 0) {
                    $saleReturn = $this->saleReturnRepository->create($input['sale_return_info']);

                    // Sale Update
                    $sale_data = Sale::findOrFail($input['returnInfo']['id']);
                    $saleUpdate = array('return_type'=>1);
                    $sale_data = $sale_data->fill($saleUpdate);
                    $sale_data->save();

                    // For Sale return items
                    $saleReturn->saleReturnItems()->saveMany($items);

                    // Account Transaction
                    $account_transaction    = $this->saleReturnAccountTransaction($sales_return_transaction_data, "return");
                    $saleReturn->update(['voucher_id' => $account_transaction->id]);



                }

                DB::commit();
                return $this->sendResponse($saleReturn, 'Sale return successfully done!');

            }catch(\Exception $e) {
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }else{
            return $this->sendError("Something went wrong please try again");
        }

    }


    // For Sales Replace and return
    public function storeSaleReplace(CreateSaleReturnAPIRequest $request)
    {
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }
        $input = $request->all();
        return "replace and return==== ".$input;

        $sale_items = $input['returnInfo']['sales_items'];
        $sale_item_array = [];
        $sale_item_discount = 0;
        $sale_item_vat  = 0;
        $sale_item_mrp  = 0;
        $sale_item_cost = 0;
        foreach ($sale_items as $sale_item) {
            $sale_item  = (object) $sale_item;
            $sale_item_array[$sale_item->id] = $sale_item;

            $sale_item_discount += ($sale_item->quantity * $sale_item->discount);
            $sale_item_vat += ($sale_item->quantity * $sale_item->vat);
            $sale_item_mrp += ($sale_item->quantity * $sale_item->mrp_price);
            $sale_item_cost += ($sale_item->quantity * $sale_item->cost_price);
        }

        // Return old Voucher
        $old_total_sale_discount = ($sale_item_discount + $input['returnInfo']['customer_discount'] + $input['returnInfo']['customer_group_discount'] + $input['returnInfo']['order_discount_value']);

        return $this->sendResponse([$input, $sale_item_array], 'Sale Return & Replace test');

        // Sale Return Item
        $total_item_discount = 0;
        $total_item_vat = 0;
        $total_item_mrp_amount = 0;
        $total_item_cost_amount = 0;
        $total_return_amount = 0;
        $return_or_replace  = 2;

        if($input['return_replace']) {
            foreach ($input['return_replace'] as $key => $value)
            {
                $sitem  = $sale_item_array[$value['item_id']];
                if($value['return_qty'] >= $value['item_qty']){
                    $return_qty = $value['item_qty'];
                } else{
                    $return_qty = $value['return_qty'];
                }

                if($value['return_or_replace'] != "" && $value['replace_pro_id'] != "" && $value['return_qty'] != "") {

                    $old_sale_item  = SaleItem::where('id', $value['item_id'])->first();
                    $new_product = Product::where('id', $value['new_pro_id'])->first();
                    $new_product_item   = new PosProductResource($new_product);

                    $new_product_stock  = StockProduct::where('id', $value['replace_stock_id'])->first();
                    $np_stock_quantity = $new_product_stock->stock_quantity;
                    $np_out_stock_quantity = $new_product_stock->out_stock_quantity;
                    $update_stock_quantity  = $np_stock_quantity - $return_qty;
                    $update_out_stock_quantity = $np_out_stock_quantity + $return_qty;


                    $item_discount = $new_product_item->item_discount * $return_qty;
                    $item_vat   = $new_product_item->tax * $return_qty;
                    $item_mrp_amount = $new_product_item->mrp_price * $return_qty;
                    $item_cost_amount = $new_product_item->cost_price * $return_qty;

                    $update_sale_item = [
                        'stock_id'  => $new_product_stock->id,
                        'product_id'    => $new_product->id,
                        'quantity'  => $return_qty,
                        'discount'  => $new_product_item->item_discount,
                        'vat'   => $new_product_item->tax,
                        'vat_id'    => $new_product_item->product_tax,
                        'mrp_price' => $new_product_item->mrp_price,
                        'cost_price'    => $new_product_item->cost_price,
                        'return_type'   => $return_or_replace
                    ];

                    $return_items[] = new SaleReturnItem(
                        [
                            'sale_id'       => $input['returnInfo']['id'],
                            'sale_item_id'  => $value['item_id'],
                            'item_pro_id'   => $value['pro_id'],
                            'sitem_stock_id'=> $sitem->stock_id,

                            'replace_pro_id'=> $new_product->id,
                            'sale_item_qty' => $value['item_qty'],
                            'sale_item_mrp_price'   => $sitem->mrp_price,
                            'sale_item_cost_price'   => $sitem->cost_price,
                            'sale_item_vat'   => $sitem->vat,
                            'sale_item_vat_id'   => $sitem->vat_id,
                            'sale_item_discount'   => $sitem->discount,
                            'sale_r_qty'    => $return_qty,
                            'return_type'   => $return_or_replace,
                        ]
                    );
                }else{
                    $item_discount = $sitem->discount * $return_qty;
                    $item_vat   = $sitem->vat * $return_qty;
                    $item_mrp_amount = $sitem->mrp_price * $return_qty;
                    $item_cost_amount = $sitem->cost_price * $return_qty;

                    $return_items[] = new SaleReturnItem(
                        [
                            'sale_id'       => $input['returnInfo']['id'],
                            'sale_item_id'  => $value['item_id'],
                            'item_pro_id'   => $value['pro_id'],
                            'sitem_stock_id'=> $sitem->stock_id,

                            'replace_pro_id'=> '',
                            'sale_item_qty' => $value['item_qty'],
                            'sale_item_mrp_price'   => $sitem->mrp_price,
                            'sale_item_cost_price'   => $sitem->cost_price,
                            'sale_item_vat'   => $sitem->vat,
                            'sale_item_vat_id'   => $sitem->vat_id,
                            'sale_item_discount'   => $sitem->discount,
                            'sale_r_qty'    => 0,
                            'return_type'   => $return_or_replace,
                        ]
                    );
                }



                $total_item_discount    += $item_discount;
                $total_item_vat += $item_vat;
                $total_item_mrp_amount  += $item_mrp_amount;
                $total_item_cost_amount += $item_cost_amount;
            }
        }

        if($input['return_replace']){
            DB::beginTransaction();
            try {
                foreach ($input['return_replace'] as $key => $value) {
                    if($value['return_or_replace']){

                        $sitem  = $sale_item_array[$value['item_id']];
                        if($value['return_qty'] >= $value['item_qty']){
                            $return_qty = $value['item_qty'];
                        } else{
                            $return_qty = $value['return_qty'];
                        }

                        $item_discount = $sitem->discount * $return_qty;
                        $item_vat   = $sitem->vat * $return_qty;
                        $item_mrp_amount = $sitem->mrp_price * $return_qty;
                        $item_cost_amount = $sitem->cost_price * $return_qty;

                        $total_item_discount    += $item_discount;
                        $total_item_vat += $item_vat;
                        $total_item_mrp_amount  += $item_mrp_amount;
                        $total_item_cost_amount += $item_cost_amount;

                        if($value['return_or_replace'] == 0){
                            $return_or_replace = 1; // return
                            $model = SaleItem::findOrFail($value['item_id']);
                            //0=default 1=return 2=replace 3=void
                            $saleUpdate = array('return_type'=>1);
                            $model = $model->fill($saleUpdate);
                            $model->save();
                        }
                        else {
                            $return_or_replace = 2; // replace
                            $product = Product::find( $value['replace_pro_id']);
                            $model = SaleItem::findOrFail($value['item_id']);
                            //0=default 1=return 2=replace 3=void
                            $saleUpdate = array(
                                'return_type'=> $return_or_replace, // Replace
                                'product_id' => $product->id,
                                'mrp_price'  => $product->mrp_price,
                                'cost_price' => $product->cost_price,
                                'vat_id'     => $product->tax_method,
                            );
                            $model = $model->fill($saleUpdate);
                            $model->save();
                        }


                        // Stock Update
                        $stock_data = StockProduct::where('id', $sitem->stock_id)->first();
                        $old_stock_quantity = $stock_data->stock_quantity;
                        $new_stock_quantity = $old_stock_quantity + $return_qty;
                        $stock_data->update(['stock_quantity' => $new_stock_quantity]);

                        //0=default 1=return 2=replace 3=void
                        $items[] = new SaleReturnItem([
                            'sale_id'       => $input['returnInfo']['id'],
                            'sale_item_id'  => $value['item_id'],
                            'item_pro_id'   => $value['pro_id'],
                            'replace_pro_id'=> ($return_or_replace ==2) ? $value['replace_pro_id'] : '',
                            'sale_item_qty' => $value['item_qty'],
                            'sale_r_qty'    => ($return_or_replace ==2) ? $value['item_qty'] : $return_qty,
                            'return_type'   => $return_or_replace,
                        ]);
                    }else{
                        $items = [];
                    }
                }

                $sales_return_transaction_data = [
                    'fiscal_year_id'    => $fiscal_year->id,
                    'total_discount_amount'    => $total_item_discount,
                    'total_vat_amount'  => $total_item_vat,
                    'total_item_mrp_amount' => $total_item_mrp_amount,
                    'total_cogs_amount'    => $total_item_cost_amount,
                    'total_cash_amount' => (($total_item_mrp_amount + $total_item_vat) - $total_item_discount),
                ];


                // For Sale return
                $input['sale_return_info']['return_type'] = 1;
                $input['sale_return_info']['sale_id'] = $input['returnInfo']['id'];
                $input['sale_return_info']['return_amount'] = $sales_return_transaction_data['total_cash_amount'];

                $saleReturn = $this->saleReturnRepository->create($input['sale_return_info']);

                // Sale Update
                $sale_data = Sale::findOrFail($input['returnInfo']['id']);
                $saleUpdate = array('return_type'=>1);
                $sale_data = $sale_data->fill($saleUpdate);
                $sale_data->save();

                // For Sale return items
                $saleReturn->saleReturnItems()->saveMany($items);

                // Account Transaction
                $account_transaction    = $this->saleReturnAccountTransaction($sales_return_transaction_data);
                $saleReturn->update(['voucher_id' => $account_transaction->id]);

                DB::commit();
                return $this->sendResponse($saleReturn, 'Sale return successfully done!');
            }catch(\Exception $e) {
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }else{
            return $this->sendError("Something went wrong please try again");
        }

    }


    // For void sales invoice
    public function saleReturnsVoid(CreateSaleReturnAPIRequest $request)
    {


        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $input = $request->all();
        echo "void return==== ";
        return $input;

        // sales info data
        $sales_info     = (object) $input['returnInfo'];

        $input['sale_return_info']['return_type'] = 3;
        $input['sale_return_info']['sale_id'] = $input['returnInfo']['id'];

        $total_discount_amount = $sales_info->sales_items_sum_discount + $sales_info->customer_discount + $sales_info->customer_group_discount;

//        return $total_discount_amount;

        $total_item_discount    = 0;
        $total_vat_amount   = 0;
        $total_item_mrp_amount = 0;
        $total_cogs_amount = 0;
        $total_cash_amount = 0;

        DB::beginTransaction();
        try {
            $saleReturn = $this->saleReturnRepository->create($input['sale_return_info']);

            $model = Sale::findOrFail($input['returnInfo']['id']);
            $saleUpdate = array('return_type' => 3);
            $model = $model->fill($saleUpdate);
            $model->save();

            SaleItem::where('sale_id', '=', $input['returnInfo']['id'])
                ->update(['return_type' => 3]);
            $saleItem = SaleItem::where('sale_id', '=', $input['returnInfo']['id'])->get();
            if($saleItem){
                foreach ($saleItem as $key => $value) {
                    // Stock Update
                    $stock_data = StockProduct::where('id', $value->stock_id)->first();
                    $old_stock_quantity = $stock_data->stock_quantity;
                    $new_stock_quantity = $old_stock_quantity + $value->quantity;
                    $stock_data->update(['stock_quantity' => $new_stock_quantity]);

                    //0=default 1=return 2=replace 3=void
                    $items[] = new SaleReturnItem([
                        'sale_id'       => $input['returnInfo']['id'],
                        'sale_item_id'  => $value->id,
                        'item_pro_id'   => $value->product_id,
                        'replace_pro_id'=> '',
                        'sale_item_qty' => $value['item_qty'],
                        'sale_r_qty'    => $value->quantity,
                        'return_type'   => 3,
                    ]);

                    $total_item_discount += $value->discount * $value->quantity;
                    $total_vat_amount += ($value->quantity * $value->vat);
                    $total_item_mrp_amount += ($value->quantity * $value->mrp_price);
                    $total_cogs_amount += ($value->quantity * $value->cost_price);

                }
                $saleReturn->saleReturnItems()->saveMany($items);


                $sales_transaction_data = [
                    'fiscal_year_id'    => $fiscal_year->id,
                    'total_discount_amount'    => $total_discount_amount,
                    'total_vat_amount'  => $total_vat_amount,
                    'total_item_mrp_amount' => $total_item_mrp_amount,
                    'total_cogs_amount'    => $total_cogs_amount,
//                    'total_cash_amount' => (($total_item_mrp_amount + $total_vat_amount) - $total_discount_amount),
                    'total_cash_amount' => ($total_item_mrp_amount - $total_discount_amount),
                ];

                $account_transaction    = $this->saleReturnAccountTransaction($sales_transaction_data);
                $saleReturn->update(['voucher_id' => $account_transaction->id]);
            }
            DB::commit();
            return $this->sendResponse($saleReturn, 'Invoice Void successfully');

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getLine());
        }
    }

    /**
     * Display the specified SaleReturn.
     * GET|HEAD /saleReturns/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    { 
        $saleReturn = SaleReturn::with(['sale' => function($query){
            $query->select('id', 'invoice_number');
        }])->withCount(['saleReturnItems AS return_item_qty' => function($query){
            $query->select(\DB::raw("SUM(sale_r_qty) as sale_r_qty"));
        }])
        ->withCount('saleReturnItems AS return_item')
        ->with('saleItems')
        ->with('saleReturnItems') 
        ->with('sale')->find($id);

        //$saleReturn = $this->saleReturnRepository->with('sale','saleReturnItems')->find($id);

        if (empty($saleReturn)) {
            return $this->sendError('Sale Return not found');
        }

        return $this->sendResponse($saleReturn->toArray(), 'Sale Return data retrieved successfully');
    }

    /**
     * Update the specified SaleReturn in storage.
     * PUT/PATCH /saleReturns/{id}
     *
     * @param int $id
     * @param UpdateSaleReturnAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSaleReturnAPIRequest $request)
    {
        $input = $request->all();

        /** @var SaleReturn $saleReturn */
        $saleReturn = $this->saleReturnRepository->find($id);

        if (empty($saleReturn)) {
            return $this->sendError('Sale Return not found');
        }

        $saleReturn = $this->saleReturnRepository->update($input, $id);

        return $this->sendResponse($saleReturn->toArray(), 'SaleReturn updated successfully');
    }

    /**
     * Remove the specified SaleReturn from storage.
     * DELETE /saleReturns/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SaleReturn $saleReturn */
        $saleReturn = $this->saleReturnRepository->find($id);

        if (empty($saleReturn)) {
            return $this->sendError('Sale Return not found');
        }

        $saleReturn->delete();

        return $this->sendSuccess('Sale Return deleted successfully');
    }


    // Sale Return Account Transactions
    protected function saleReturnAccountTransaction($data=array(), $return_type="void_return")
    {
        if($return_type == "void_return") {
            $gnote  = "Product Sale return void invoice";
        }else{
            $gnote  = "Product Sale return";
        }
        $entry_type = EntryType::where('label', 'journal')->first();
        $voucher_code   = $this->returnVoucherCode('journal');
        $account_voucher_inputs  = [
            'vcode' => $voucher_code,
            'vtype_id'  => $entry_type->id,
            'vtype_value'   => 'auto voucher',
            'fiscal_year_id'    => $data['fiscal_year_id'],
            'vdate' => date("Y-m-d"),
            'global_note'   => $gnote,
            'modified_item' => 0,
        ];

        $account_default_setting    = AccountDefaultSetting::first();

        // Cash Sales Return
        // 1st Transaction
        $cash_sale_ledger = getLedgerAccountById($account_default_setting->cash_sales_account); // Dr income
        $cash_ledger = getLedgerAccountById($account_default_setting->cash_in_hand_account); // cr assets


//        $cash_sale_ledger = $this->getLedgerData('ledger_code', '410101'); // Dr income
//        $vat_payable_ledger = $this->getLedgerData('ledger_code', '210204'); // dr liability
//        $cash_ledger = $this->getLedgerData('ledger_code', '120701'); // cr assets
//        $discount_ledger = $this->getLedgerData('ledger_code', '511701'); // cr expense

        // 2nd Transaction
        $inventory_ledger = getLedgerAccountById($account_default_setting->inventory_account); // dr assets
        $cogs_ledger = getLedgerAccountById($account_default_setting->cogs_account); // cr expense
//        $inventory_ledger = $this->getLedgerData('ledger_code', '120101'); // dr assets
//        $cogs_ledger = $this->getLedgerData('ledger_code', '511901'); // cr expense

        $transactions    = [
            // first Transaction
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'dr',
                'ledger_id' => $cash_sale_ledger->id,
                'ledger_code' => $cash_sale_ledger->ledger_code,
                'debit' => $data['total_cash_amount'],
                'credit'    => 0,
                'reference_id'  => null,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
//            new AccountVoucherTransaction([
//                'cost_center_id'    => 2,
//                'vaccount_type'   => 'dr',
//                'ledger_id' => $vat_payable_ledger->id,
//                'ledger_code' => $vat_payable_ledger->ledger_code,
//                'debit' => $data['total_vat_amount'],
//                'credit'    => 0,
//                'reference_id'  => $cash_sale_ledger->ledger_code,
//                'transaction_sl'    => 1,
//                'voucher_note'  => null,
//                'balance'   => 0,
//                'created_at'    => date("Y-m-d H:i:s"),
//                'updated_at'    => date("Y-m-d H:i:s"),
//            ]),
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'cr',
                'ledger_id' => $cash_ledger->id,
                'ledger_code' => $cash_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $data['total_cash_amount'],
                'reference_id'  => $cash_sale_ledger->ledger_code,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
//            new AccountVoucherTransaction([
//                'cost_center_id'    => 2,
//                'vaccount_type'   => 'cr',
//                'ledger_id' => $discount_ledger->id,
//                'ledger_code' => $discount_ledger->ledger_code,
//                'debit' => 0,
//                'credit'    => $data['total_discount_amount'],
//                'reference_id'  => $cash_sale_ledger->ledger_code,
//                'transaction_sl'    => 1,
//                'voucher_note'  => null,
//                'balance'   => 0,
//                'created_at'    => date("Y-m-d H:i:s"),
//                'updated_at'    => date("Y-m-d H:i:s"),
//            ]),

            //2nd Transaction
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'dr',
                'ledger_id' => $inventory_ledger->id,
                'ledger_code' => $inventory_ledger->ledger_code,
                'debit' => $data['total_cogs_amount'],
                'credit'    => 0,
                'reference_id'  => null,
                'transaction_sl'    => 2,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'cr',
                'ledger_id' => $cogs_ledger->id,
                'ledger_code' => $cogs_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $data['total_cogs_amount'],
                'reference_id'  => $inventory_ledger->ledger_code,
                'transaction_sl'    => 2,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),

        ];

        $transaction_array = [];
        foreach ($transactions as $transaction) {
            if($transaction->debit > 0 || $transaction->credit > 0) {
                $transaction_array[] = $transaction;
            }
        }
        $voucher_save = AccountVoucher::create($account_voucher_inputs);
        $transactions_save = $voucher_save->account_voucher_transactions()->saveMany($transaction_array);

        return $voucher_save;


    }

    protected function getLedgerData($key, $value)
    {
        $ledger_data    = AccountLedger::where($key, $value)->first();

        return $ledger_data;
    }
}
