<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesAPIRequest;
use App\Http\Requests\API\UpdateSalesAPIRequest;
use App\Http\Controllers\API\PointsSettingsAPIController;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\Sale;
use App\Models\PaymentCollection;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\Customer;
use App\Models\UsersPoints;
use App\Models\PointsSettings;
use App\Models\CustomerLedger;
use App\Models\CustomerGroup;
use App\Models\District;
use App\Models\MobileWallet;
use App\Models\Unit; 
use App\Models\HoldSale;
use App\Models\HoldSaleItem;
use App\Models\SalesDiscount;
use App\Models\BankAccount;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Myclass\SMS;
use App\Repositories\SalesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\ProductResource;
use Response;
use DB;
use Carbon\Carbon; 
use App\DataGrid\SalesGrid;
use Auth;
/**
 * Class SaleController
 * @package App\Http\Controllers\API
 */

class SaleAPIController extends AppBaseController
{
    /** @var  SaleRepository */
    private $saleRepository;

    public function __construct(SalesRepository $saleRepo)
    {
        $this->saleRepository = $saleRepo;
    }

    /**
     * Display a listing of the Sale.
     * GET|HEAD /sale
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pageLength = request('pageLength') ?? 10;
        $sales = Sale::filtered()->withCount('salesItems');

        // $posts = (new SalesGrid())->render();
    
        // return ['success' => true, 'collection' => $posts];

        //$return_data = PosProductResource::collection($products2);
        return response()->json($sales->paginate($pageLength), 200);


        // if($request->get('list') =='list'){
        //    return $sale = $this->list($request);
        // }else{
        //     $sale = $this->saleRepository->all(
        //         $request->except(['skip', 'limit']),
        //         $request->get('skip'),
        //         $request->get('limit')
        //     ); 
        // }

        return $this->sendResponse($sale->toArray(), 'Sale retrieved successfully');
    }

    public function list(Request $request)
    {

        $columns = ['id','created_at', 'invoice_number', 'total_amount', 'customer_name', 'collection_amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $from_date  = $request->input('from_date') ?? null; //Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date    = $request->input('to_date') ?? Carbon::now()->format("Y-m-d");
        $outlet_id  = $request->input('outlet_id');

        $query = Sale::with(['salesItems', 'outlets'])
        ->select('id','created_at', 'invoice_number', 'grand_total', 'total_amount', 'order_discount_value', 'customer_discount', 'customer_group_discount', 'outlet_id', 'customer_name', 'collection_amount')
        ->where('return_type', '!=', 3)->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('invoice_number', 'like', '%' .$searchValue. '%');
                $query->orWhere('collection_amount', 'like', '%' .$searchValue. '%');
                $query->orWhere('customer_name', 'like', '%' .$searchValue. '%');
            });
        }

        if(isset($outlet_id)) {
            $query->where('outlet_id', $outlet_id);
        }

        // if(isset($from_date)) {
        //     $query->whereDate('created_at', '>=', $from_date);
        // }

        if(isset($to_date)) {
            $query->whereDate('created_at', '<=', $to_date);
        }


        $sales_data = $query->withCount('salesItems')
//            ->withSum('salesItems', 'discount')
            ->addSelect(['sales_items_sum_discount' => SaleItem::whereColumn('sale_id', 'sales.id')->selectRaw('sum(quantity * discount) as sales_items_sum_discount')])
            ->withSum('salesItems', 'mrp_price')
            ->paginate($length);
        $return_data    = [
            'data' => $sales_data,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Sales retrieved successfully');
    }

    public function duelist(Request $request)
    { 

        $columns = ['id','created_at', 'invoice_number', 'grand_total', 'total_amount', 'customer_name', 'collection_amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = Sale::select('id','created_at', 'invoice_number', 'grand_total', 'total_amount', 'customer_name', 'collection_amount')->where('return_type', '!=', 3)->orderBy($columns[$column], $dir)
            ->where('status', '!=','paid');

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('invoice_number', 'like', '%' .$searchValue. '%');
                $query->orWhere('collection_amount', 'like', '%' .$searchValue. '%'); 
                $query->orWhere('customer_name', 'like', '%' .$searchValue. '%'); 
            });
        }

        $areas = $query->withCount('salesItems')->paginate($length);
        $return_data    = [
            'data' => $areas,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Areas retrieved successfully');
    }

    /**
     * Store a newly created Sale in storage.
     * POST /sale
     *
     * @param CreateSalesAPIRequest $request
     *
     * @return Response
     */
    public function checksms(){ 
        echo date('d'). strtoupper(date('M')).date('y');
        exit();
        $number = '01821915515';
        $data = GeneralSetting::first();
        $smsItemInfo = array(array(
                'pro_name' => 'GRASS CARP', 
                'wt' =>'W: 2.56 KG', 
                'price'=> 'R: TK 300/KG', 
                'total' => 'T: 768 TK'
            ),
            array(
                'pro_name' => 'GRASS CARP', 
                'wt' =>'W: 2.56 KG', 
                'price'=> 'R: TK 300/KG', 
                'total' => 'T: 768 TK'
            ));

        $mess =  implode(', ', array_map(function ($smsItem) {
            $datya = $smsItem['pro_name'].', '.$smsItem['wt'].', '.$smsItem['price'].', '.$smsItem['total'];
          return  $datya;
        }, $smsItemInfo));
        $var = date('Y-m-d');  
        if($number){
            //$mes = 'this is test message from 24x7pos';
            $sms = new SMS();
            $test = $sms->send($number, $mess); 
            echo $test; 
        }
    }

    public function sendInvoiceSMS($number, $item_info, $paymentinfo, $payment_type, $date, $order_call ){ 
        $genaralSetting = GeneralSetting::first();
        $itemInfo =  implode(', ', array_map(function ($smsItem) {
            $smsdata = $smsItem['pro_name'].', '.$smsItem['wt'].', '.$smsItem['price'];
          return $smsdata;
        }, $item_info));
        $smsfooterText = $genaralSetting? $genaralSetting->sms_text : "THANKS FOR BEING WITH SSG AGRO, FOR ORDER CALL {$order_call}";
        // $dateFormat = $genaralSetting? ($genaralSetting->date_status ? ' DT: '.date($genaralSetting->date_format, strtotime($date)).',' : '') : '';
        $paymentMethod = '';
        $dateFormat = 'DT: '. date('d'). strtoupper(date('M')).date('y'); 
        if($paymentinfo['due'] > 1){
            $paymenStatu = 'T:'.$paymentinfo['grand_total'].'tk, DUE:'.$paymentinfo['due'].'tk,';
        }else{
            $paymentMethod = "\n".$genaralSetting? ($genaralSetting->payment_status ? "RCV: {$payment_type}," : '') : '';
            $paymenStatu = 'T:'.$paymentinfo['grand_total'].'tk, PAID:'.$paymentinfo['collection_amount'].'tk,';
        } 

        if(($number) && ($genaralSetting? $genaralSetting->invoice_sms_status : 0)) {
            $mes = "{$itemInfo},{$paymentMethod}\n{$paymenStatu}{$dateFormat}\n$smsfooterText";
            $sms = new SMS();
            $smsReturnData = $sms->send($number, $mes);
        }
    }

    public function storeOld(CreateSalesAPIRequest $request)
    {
        $this->validate($request, [
            'customer_id'  => 'required', 
        ]);  
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $input = $request->all();

//        return $input;

        $pointsSettings = PointsSettings::first();
        $payments = [];
        if($request->hold_sale_info) {
            $holdSale = HoldSale::find($request->hold_sale_info['id']);   
            $holdSale->salesItems()->forceDelete();
            $holdSale->forceDelete();
        }

        $redeem_point=false;
        $total_value_amount = 0; // For account transaction

        if ($request->payments) {
            $getPayments = $request->payments;  
            $paidByCount = 0;
            foreach ($getPayments as $key => $value) { 
                if($value['paid_by'] =='cash'){
                    if($paidByCount ==0){
                        $paidByCount++;
                        $value['amount'] = $value['amount'] - $request->return_amount; 
                    }
                } else if($value['paid_by'] =='redeem_point'){                    
                    $convartRate = $pointsSettings->cart_price_rate / $pointsSettings->cart_points_rate;
                    $value['amount'] = ($convartRate * $value['redeem_point']);  
                    $redeem_point=true;
                    $redeemValue = $value['redeem_point'];
                }
                $payments[] = new PaymentCollection([
                    'amount'        => $value['amount'],
                    'credit_card'   => $value['card_reference_code'],
                    'gift_card'     => $value['gift_card'],
                    'paying_by'     => $value['paid_by'],
                    'payment_note'  => $value['payment_note'],
                    'wallet_id'     => $value['wallet_id'],
                ]);

                $total_value_amount += $value['amount'];
            } 
        }

        $items = [];
        $smsItemInfo = [];
        $update_stock = [];
        $salesDiscount = [];

        $total_item_discount = 0;
        $total_item_vat = 0;
        $total_item_mrp_amount = 0;
        $total_item_cost_amount = 0;

        if ($request->items) {
            $getItems = $request->items;  
            foreach ($getItems as $key => $value) {  
                $product = Product::find($value['product_id']);  
                $stockPro = StockProduct::where('product_id', $value['product_id'])
                                ->where('id', $value['product_stock_id'])->first(); 
                $update_stock_arr = [
                    'id' => $stockPro->id,
                    'out_stock_quantity' => $stockPro->out_stock_quantity + $value['quantity'],
                    'stock_quantity'    => $stockPro->stock_quantity - $value['quantity'],
                    'out_stock_weight' => $stockPro->out_stock_weight + $value['weight'], 
                    'stock_weight'    => $stockPro->stock_weight ? ($stockPro->stock_weight - $value['weight']) : $stockPro->stock_weight,
                    'in_stock_weight'  => $stockPro->in_stock_weight + $value['weight'], 
                ];
                $update_stock[] = $update_stock_arr;

                $items[] = new SaleItem([
                    'stock_id'=> $value['product_stock_id'],
                    'product_id'=> $value['product_id'],
                    'quantity'  => $value['quantity'],
                    'discount'  => $value['discount'], 
                    'vat'       => $value['tax'],
                    'vat_id'    => $product->product_tax, 
                    'mrp_price' => $product->mrp_price,
                    'cost_price'=> $product->cost_price,
                    'uom'       => $value['uom'],
                    'weight'    => $value['weight']
                ]);
                if($value['weight']){
                    $smsItemInfo[] = array(
                        'pro_name' => strtoupper($product->product_native_name), 
                        'wt' => $value['weight'].'KG', 
                        'price'=> $product->mrp_price.'tk',  
                    );
                }else{
                    $smsItemInfo[] = array(
                        'pro_name' => strtoupper($product->product_native_name), 
                        'wt' => $value['quantity'], 
                        'price'=> $product->mrp_price.'tk',  
                    );
                }
                
                if(sizeof($value['dis_array']) > 0 ){ 
                    foreach ($value['dis_array'] as $key2 => $dis_array) {    
                        if($dis_array > 0){
                            $salesDiscount[$key][] =  new SalesDiscount([  
                                'key'   => $key2, 
                                'value' => $dis_array, 
                            ]);  
                        } 
                    }  
                }

                $total_item_discount += ($value['quantity'] * $value['discount']);
                $total_item_vat += $value['tax'];
                $total_item_mrp_amount += ($value['quantity'] * $product->mrp_price);
                $total_item_cost_amount += ($value['quantity'] * $product->cost_price);

            }
        } 

        
        $customer = Customer::find($request->customer_id);   
        $id=\DB::select("show table status where name='sales'; ");
        $next_sale_id=$id[0]->Auto_increment; 
        $outlet_id = auth('api')->user() ? (auth('api')->user()->outlet_id ? auth('api')->user()->outlet_id : 1 ) : 1;
        if ($request->items) {
            $salesData = array(
                'customer_id' => $request->customer_id,
                'invoice_number' => 'INV'.sprintf('%03d',$outlet_id).date('y').sprintf('%06d',$next_sale_id),
                'customer_name' => $customer->name,
                'total_amount' => $request->total_amount,
                'grand_total' => $request->grand_total,
                'collection_amount' => $request->total_collect_amount,
                'paid_amount' => $request->paid_amount,
                'return_amount' => $request->return_amount,
                'sale_type' => 'pos',
                'customer_discount' => $request->customer_discount ? $request->customer_discount : 0,
                'customer_group_discount' => $request->customer_group_discount ? $request->customer_group_discount : 0,
                'order_discount' => $request->order_discount,
                'order_discount_value' => $request->order_discount_value,
                'order_vat' => $request->order_vat, 
                'order_items_vat' => $request->order_items_vat, 
                'outlet_id' => $outlet_id, 
                'status' => $request->status,
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'created_by' => Auth::user()->id,
            ); 
        }
        
        if(($pointsSettings->enable_points_rewards) && ($pointsSettings->enable_points_order_total)){ 
            foreach ($pointsSettings->points_within_order_range as $key => $value) { 
                if(($value['minimum'] < $request->grand_total) && ($value['maximum'] > $request->grand_total)){
                    $points = $value['points'];
                }else{
                    $points = $request->grand_total/100;
                }
            }
        }else if($pointsSettings->enable_points_rewards){
            $points = $request->grand_total/100;
        }else{
            $points = '';
        }
        $insertPoint = new UsersPoints();
        $insertPoint->user_id = $request->customer_id;
        $insertPoint->type = 'insert';
        $insertPoint->points = $points;

        if($redeem_point){
            $redeemPoint = new UsersPoints();
            $redeemPoint->user_id = $request->customer_id;
            $redeemPoint->type = 'redeem';
            $redeemPoint->points = $redeemValue;
        } 

        $CustomerLedger = CustomerLedger::orderBy('id', 'DESC')->where('customer_id',$request->customer_id)->first();
        if(empty($CustomerLedger)) {
            $customer_closing_balance = 0;
            $closing_balance = $request->grand_total - $request->total_collect_amount;
        } else {
            $customer_closing_balance = $CustomerLedger->closing_balance;
            $diff = $request->grand_total - $request->total_collect_amount;
            $closing_balance = ($customer_closing_balance + $diff);
        }

        $customer_ledger_data = new CustomerLedger();
        $customer_ledger_data->customer_id = $request->get('customer_id');
        $customer_ledger_data->transaction_type = 'sale';
        $customer_ledger_data->note = 'POS Sale';
        $customer_ledger_data->debit_amount = $request->grand_total;
        $customer_ledger_data->credit_amount = $request->total_collect_amount;
        $customer_ledger_data->opening_balance = $customer_closing_balance;
        $customer_ledger_data->closing_balance = $closing_balance;
        $customer_ledger_data->transaction_date = date("Y-m-d");

        /** For Sales Account Transaction  */
        $cust_discount = $request->customer_discount ? $request->customer_discount : 0;
        $cust_group_discount = $request->customer_group_discount ? $request->customer_group_discount : 0;
        $order_discount = $request->order_discount_value;

        $total_discount = ($cust_discount + $cust_group_discount + $order_discount + $total_item_discount);
        $total_vat_amount = ($request->order_items_vat + $request->order_vat);

        $sales_transaction_data = [
            'fiscal_year_id'    => $fiscal_year->id,
            'total_discount'    => $total_discount,
            'total_vat_amount'  => $total_vat_amount,
            'total_item_mrp_amount' => $total_item_mrp_amount,
            'total_cogs_amount'    => $total_item_cost_amount,
            'total_cash_amount' => (($total_item_mrp_amount + $total_vat_amount) - $total_discount),
            'sale_status'   => $request->status,
            'paid_amount'   => $request->paid_amount,
        ];
        /** For Sales Account Transaction  */


        // return $sales_transaction_data; 
        // return $this->sendResponse([$items, $salesData, $sales_transaction_data], 'Sale saved successfully');

        DB::beginTransaction();
        try{ 
            $sale = $this->saleRepository->create($salesData);
            if($pointsSettings->enable_points_rewards){
                $sale->point()->save($insertPoint);
            }

            foreach ($update_stock as $key => $value) {
                StockProduct::where('id',$value['id'])->update([
                    'out_stock_quantity'=> $value['out_stock_quantity'],
                    'stock_quantity' => $value['stock_quantity'],
                    'in_stock_weight' => $value['in_stock_weight'],                    
                    'stock_weight'   => $value['stock_weight'], 
                    'out_stock_weight' => $value['out_stock_weight'],
                ]); 
            }

            if($redeem_point){
               $sale->point()->save($redeemPoint); 
            }

            $ddd = array();
            $itemsInstance = $sale->salesItems()->saveMany($items);
            $i = 0;
            foreach ($itemsInstance as $key => $itemsInstanc) {
                //SaleItem::find($itemsInstanc->id);
                //$saleDataItem = SaleItem::find($itemsInstanc->id);
                //$saleDataItem->salesDiscount()->saveMany($salesDiscount[$i]);
                $itemsInstanc->salesDiscount()->saveMany($salesDiscount[$i]);
                // for($j=0; $j<count($salesDiscount[$i]); $j++) {
                //     $salesDiscount[$i][$j]['sale_item_id'] = $itemsInstanc->id;
                //     $salesDiscount[$i][$j]['created_at'] = date("Y-m-d H:i:s");
                //     $salesDiscount[$i][$j]['updated_at'] = date("Y-m-d H:i:s");
                // }
                // (new SalesDiscount)->insert($salesDiscount[$i]);
               $i++;
            }

            $sale->payments()->saveMany($payments);
            $sale->customerLedger()->save($customer_ledger_data);

            // Account Transaction
            $account_transaction = $this->saleAccountTransaction($sales_transaction_data);
            $sale->update(['voucher_id' => $account_transaction->id]);

            // sale data query
            $query = Sale::with(['payments','salesItems','customer','createdBy','salesDiscount']);
            $query->where('invoice_number',$sale->invoice_number);
            $saleInfo = $query->get()->toArray(); 

            // Sms 
            // $smsItemInfo = array(
            //     'pro_name' => 'GRASS CARP', 
            //     'wt' =>'W: 2.56 KG', 
            //     'price'=> 'R: TK 300/KG', 
            //     'total' => 'T: 768 TK'
            // );
            $payment_type = 'CASH';
            $date = date('Ymd');
            $order_call = '09610774774';
            $paymentinfo = array(
                'grand_total' => $request->grand_total,
                'collection_amount' => $request->total_collect_amount,
                'due' => ($request->grand_total - $request->total_collect_amount)
            );

            $this->sendInvoiceSMS(($customer->phone ? $customer->phone : $order_call ),$smsItemInfo, $paymentinfo, $payment_type, $date, $order_call );
            DB::commit();
            return $this->sendResponse($saleInfo, 'Sale saved successfully');

        }catch(\Exception $e){
            // \DB::rollback();
            return $this->sendError($e->getTrace());
        } 


    }

    public function store(CreateSalesAPIRequest $request)
    {
        $this->validate($request, [
            'customer_id'  => 'required',
        ]);
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $input = $request->all(); 

        $pointsSettings = PointsSettings::first();
        $payments = [];
        if($request->hold_sale_info) {
            $holdSale = HoldSale::find($request->hold_sale_info['id']);
            $holdSale->salesItems()->forceDelete();
            $holdSale->forceDelete();
        }

        $redeem_point=false;
        $total_value_amount = 0; // For account transaction

        if ($request->payments) {
            $getPayments = $request->payments;
            $paidByCount = 0;
            foreach ($getPayments as $key => $value) {
                if($value['paid_by'] =='cash'){
                    if($paidByCount ==0){
                        $paidByCount++;
                        $value['amount'] = $value['amount'] - $request->return_amount;
                    }
                } else if($value['paid_by'] =='redeem_point'){
                    $convartRate = $pointsSettings->cart_price_rate / $pointsSettings->cart_points_rate;
                    $value['amount'] = ($convartRate * $value['redeem_point']);
                    $redeem_point=true;
                    $redeemValue = $value['redeem_point'];
                }
                $payments[] = new PaymentCollection([
                    'amount'        => $value['amount'],
                    'card_reference_no'   => $value['card_reference_code'] ?? NULL,
                    'bank_id'       => $value['bank_id'] ?? NULL,
                    'paying_by'     => $value['paid_by'],
                    'payment_note'  => $value['payment_note'],
                    'wallet_id'     => $value['wallet_id'],
                    'transaction_no'     => $value['transaction_no'] ?? NULL,
                ]);

                $total_value_amount += $value['amount'];
            }
        }

        $items = [];
        $smsItemInfo = [];
        $update_stock = [];
        $salesDiscount = [];

        $total_item_discount = 0;
        $total_item_vat = 0;
        $total_item_mrp_amount = 0;
        $total_item_cost_amount = 0;

        if ($request->items) {
            $getItems = $request->items;
            foreach ($getItems as $key => $value) {
                $product = Product::find($value['product_id']);
                $stockPro = StockProduct::where('product_id', $value['product_id'])
                    ->where('id', $value['product_stock_id'])->first();
                $update_stock_arr = [
                    'id' => $stockPro->id,
                    'out_stock_quantity' => $stockPro->out_stock_quantity + $value['quantity'],
                    'stock_quantity'    => $stockPro->stock_quantity - $value['quantity'],
                    'out_stock_weight' => $stockPro->out_stock_weight + $value['weight'],
                    'stock_weight'    => $stockPro->stock_weight ? ($stockPro->stock_weight - $value['weight']) : $stockPro->stock_weight, 
                ];
                $update_stock[] = $update_stock_arr;

                $items[] = new SaleItem([
                    'stock_id'=> $value['product_stock_id'],
                    'product_id'=> $value['product_id'],
                    'quantity'  => $value['quantity'],
                    'discount'  => $value['discount'],
                    'vat'       => $value['tax'],
                    'vat_id'    => $product->product_tax,
                    'mrp_price' => $product->mrp_price,
                    'cost_price'=> $product->cost_price,
                    'uom'       => $value['uom'],
                    'weight'    => $value['weight']
                ]);

                /** For SMS  */
//                if($value['weight']){
//                    $smsItemInfo[] = array(
//                        'pro_name' => strtoupper($product->product_native_name),
//                        'wt' => $value['weight'].'KG',
//                        'price'=> $product->mrp_price.'tk',
//                    );
//                }else{
//                    $smsItemInfo[] = array(
//                        'pro_name' => strtoupper($product->product_native_name),
//                        'wt' => $value['quantity'],
//                        'price'=> $product->mrp_price.'tk',
//                    );
//                }

                if(sizeof($value['dis_array']) > 0 ){
                    foreach ($value['dis_array'] as $key2 => $dis_array) {
                        if($dis_array > 0){
                            $salesDiscount[$key][] =  new SalesDiscount([
                                'key'   => $key2,
                                'value' => $dis_array,
                            ]);
                        }
                    }
                }

                $total_item_discount += ($value['quantity'] * $value['discount']);
                $total_item_vat += $value['tax'];
                $total_item_mrp_amount += ($value['quantity'] * $product->mrp_price);
                $total_item_cost_amount += ($value['quantity'] * $product->cost_price);

            }
        }


        $customer = Customer::find($request->customer_id);
        $customer_group_id  = $request->get('customer_group_id');
        $customer_group_name    = strtolower($request->get('customer_group_name'));

        $id=\DB::select("show table status where name='sales'; ");
        $next_sale_id=$id[0]->Auto_increment;
        $outlet_id = auth('api')->user() ? (auth('api')->user()->outlet_id ? auth('api')->user()->outlet_id : 1 ) : 1;
        if ($request->items) {
            $salesData = array(
                'invoice_number' => 'INV'.sprintf('%03d',$outlet_id).date('y').sprintf('%06d',$next_sale_id),
                'customer_id' => $request->customer_id,
                'customer_name' => $customer->name,
                'total_amount' => $request->total_amount,
                'grand_total' => round($request->grand_total),
                'collection_amount' => $request->total_collect_amount,
                'paid_amount' => $request->paid_amount,
                'return_amount' => $request->return_amount,
                'sale_type' => 'pos',
                'customer_discount' => $request->customer_discount ? round($request->customer_discount) : 0,
                'customer_group_discount' => $request->customer_group_discount ? round($request->customer_group_discount) : 0,
                'order_discount' => $request->order_discount,
                'order_discount_value' => $request->order_discount_value,
                'order_vat' => $request->order_vat,
                'order_items_vat' => $request->order_items_vat,
                'outlet_id' => $outlet_id,
                'status' => $request->status,
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note,
                'created_by' => Auth::user()->id,
            );
        }

        if(($pointsSettings->enable_points_rewards) && ($pointsSettings->enable_points_order_total)){
            foreach ($pointsSettings->points_within_order_range as $key => $value) {
                if(($value['minimum'] < $request->grand_total) && ($value['maximum'] > $request->grand_total)){
                    $points = $value['points'];
                }else{
                    $points = $request->grand_total/100;
                }
            }
        }else if($pointsSettings->enable_points_rewards){
            $points = $request->grand_total/100;
        }else{
            $points = '';
        }

        $insertPoint = '';
        $redeemPoint = '';
        if(strtolower($customer_group_name) != "walking customer") {
            $insertPoint = new UsersPoints();
            $insertPoint->user_id = $request->customer_id;
            $insertPoint->type = 'insert';
            $insertPoint->points = $points;

            if ($redeem_point) {
                $redeemPoint = new UsersPoints();
                $redeemPoint->user_id = $request->customer_id;
                $redeemPoint->type = 'redeem';
                $redeemPoint->points = $redeemValue;
            }
        }

        // Customer Ledger and account transaction option part
        //---------------- modified --- reference SaleAccountPostingController


        DB::beginTransaction();
        try{
            $sale = $this->saleRepository->create($salesData);
            if($pointsSettings->enable_points_rewards && $insertPoint != ''){
                $sale->point()->save($insertPoint);
            }

            foreach ($update_stock as $key => $value) {
                StockProduct::where('id',$value['id'])->update([
                    'out_stock_quantity'=> $value['out_stock_quantity'],
                    'stock_quantity' => $value['stock_quantity'], 
                    'stock_weight'   => $value['stock_weight'],
                    'out_stock_weight' => $value['out_stock_weight'],
                ]);
            }

            if($redeem_point && $redeemPoint != ''){
                $sale->point()->save($redeemPoint);
            }

            $ddd = array();
            $itemsInstance = $sale->salesItems()->saveMany($items);
            $i = 0;
            foreach ($itemsInstance as $key => $itemsInstanc) {
                //SaleItem::find($itemsInstanc->id);
                //$saleDataItem = SaleItem::find($itemsInstanc->id);
                //$saleDataItem->salesDiscount()->saveMany($salesDiscount[$i]);
                $itemsInstanc->salesDiscount()->saveMany($salesDiscount[$i]);
                // for($j=0; $j<count($salesDiscount[$i]); $j++) {
                //     $salesDiscount[$i][$j]['sale_item_id'] = $itemsInstanc->id;
                //     $salesDiscount[$i][$j]['created_at'] = date("Y-m-d H:i:s");
                //     $salesDiscount[$i][$j]['updated_at'] = date("Y-m-d H:i:s");
                // }
                // (new SalesDiscount)->insert($salesDiscount[$i]);
                $i++;
            }
            if($request->status != 'due'){
                $sale->payments()->saveMany($payments);
            }

//            $sale->customerLedger()->save($customer_ledger_data); // remove line ---------------- modified

            // Account Transaction remove line ---------------- modified
//            $account_transaction = $this->saleAccountTransaction($sales_transaction_data);
//            $sale->update(['voucher_id' => $account_transaction->id]);

            // sale data query
            $query = Sale::with(['payments','salesItems','customer','createdBy','salesDiscount']);
            $query->where('invoice_number',$sale->invoice_number);
            $saleInfo = $query->get()->toArray();

            // This for Send SMS
//            $payment_type = 'CASH';
//            $date = date('Ymd');
//            $order_call = '09610774774';
//            $paymentinfo = array(
//                'grand_total' => $request->grand_total,
//                'collection_amount' => $request->total_collect_amount,
//                'due' => ($request->grand_total - $request->total_collect_amount)
//            );

//            $this->sendInvoiceSMS(($customer->phone ? $customer->phone : $order_call ),$smsItemInfo, $paymentinfo, $payment_type, $date, $order_call );
            // ---------------- modified

            DB::commit();
            return $this->sendResponse($saleInfo, 'Sale saved successfully');

        }catch(\Exception $e){
            // \DB::rollback();
            return $this->sendError($e->getTrace());
        }


    }

    protected function saleAccountTransaction($data=array())
    {
        $entry_type = EntryType::where('label', 'journal')->first();
        $voucher_code   = $this->returnVoucherCode('journal');
        $account_voucher_inputs  = [
            'vcode' => $voucher_code,
            'vtype_id'  => $entry_type->id,
            'vtype_value'   => 'auto voucher',
            'fiscal_year_id'    => $data['fiscal_year_id'],
            'vdate' => date("Y-m-d"),
            'global_note'   => 'Product cash sales',
            'modified_item' => 0,
        ];

        // 1st Transaction
        if($data['sale_status'] == 'due'){
            $cash_ledger = "";
            $account_receivable_ledger = $this->getLedgerData('ledger_code', '120201'); // dr assets
        }elseif ($data['sale_status'] == 'partial') {
            $cash_ledger = $this->getLedgerData('ledger_code', '120701'); // dr assets
            $account_receivable_ledger = $this->getLedgerData('ledger_code', '120201'); // dr assets
        }else{
            $cash_ledger = $this->getLedgerData('ledger_code', '120701'); // dr assets
            $account_receivable_ledger = "";
        }
        $discount_ledger = $this->getLedgerData('ledger_code', '511701'); // dr expense
        $cash_sale_ledger = $this->getLedgerData('ledger_code', '410101'); // cr income
        $vat_payable_ledger = $this->getLedgerData('ledger_code', '210204'); // cr liability

        // 2nd Transaction
        $cogs_ledger = $this->getLedgerData('ledger_code', '511901'); // dr expense
        $inventory_ledger = $this->getLedgerData('ledger_code', '120101'); // cr assets

        if($cash_ledger != "" && $account_receivable_ledger != "") {

            $reference_ledger_code = $cash_ledger->ledger_code;
            $cash_transaction = new AccountVoucherTransaction([
                'cost_center_id' => 2,
                'vaccount_type' => 'dr',
                'ledger_id' => $cash_ledger->id,
                'ledger_code' => $cash_ledger->ledger_code,
                'debit' => $data['paid_amount'],
                'credit' => 0,
                'reference_id' => null,
                'transaction_sl' => 1,
                'voucher_note' => null,
                'balance' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            $account_receive_transaction = new AccountVoucherTransaction([
                'cost_center_id' => 2,
                'vaccount_type' => 'dr',
                'ledger_id' => $account_receivable_ledger->id,
                'ledger_code' => $account_receivable_ledger->ledger_code,
                'debit' => $data['total_cash_amount'] - $data['paid_amount'],
                'credit' => 0,
                'reference_id' => $reference_ledger_code,
                'transaction_sl' => 1,
                'voucher_note' => null,
                'balance' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }else if($account_receivable_ledger != "")
        {
            $cash_transaction   = "";
            $account_receive_transaction = new AccountVoucherTransaction([
                'cost_center_id' => 2,
                'vaccount_type' => 'dr',
                'ledger_id' => $account_receivable_ledger->id,
                'ledger_code' => $account_receivable_ledger->ledger_code,
                'debit' => $data['total_cash_amount'],
                'credit' => 0,
                'reference_id' => null,
                'transaction_sl' => 1,
                'voucher_note' => null,
                'balance' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            $reference_ledger_code = $account_receivable_ledger->ledger_code;
        }else{
            $reference_ledger_code = $cash_ledger->ledger_code;
            $cash_transaction = new AccountVoucherTransaction([
                'cost_center_id' => 2,
                'vaccount_type' => 'dr',
                'ledger_id' => $cash_ledger->id,
                'ledger_code' => $cash_ledger->ledger_code,
                'debit' => $data['total_cash_amount'],
                'credit' => 0,
                'reference_id' => null,
                'transaction_sl' => 1,
                'voucher_note' => null,
                'balance' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            $account_receive_transaction = "";
        }

        $transactions    = [
            // first Transaction
            $cash_transaction,
            $account_receive_transaction,
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'dr',
                'ledger_id' => $discount_ledger->id,
                'ledger_code' => $discount_ledger->ledger_code,
                'debit' => $data['total_discount'],
                'credit'    => 0,
                'reference_id'  => $reference_ledger_code,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'cr',
                'ledger_id' => $cash_sale_ledger->id,
                'ledger_code' => $cash_sale_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $data['total_item_mrp_amount'],
                'reference_id'  => $reference_ledger_code,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'cr',
                'ledger_id' => $vat_payable_ledger->id,
                'ledger_code' => $vat_payable_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $data['total_vat_amount'],
                'reference_id'  => $reference_ledger_code,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),

            //2nd Transaction
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'dr',
                'ledger_id' => $cogs_ledger->id,
                'ledger_code' => $cogs_ledger->ledger_code,
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
                'ledger_id' => $inventory_ledger->id,
                'ledger_code' => $inventory_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $data['total_cogs_amount'],
                'reference_id'  => $cogs_ledger->ledger_code,
                'transaction_sl'    => 2,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),

        ];

        $transaction_array = [];
        foreach ($transactions as $transaction) {
            if($transaction != "" ) {
                if (($transaction->debit > 0 || $transaction->credit > 0)) {
                    $transaction_array[] = $transaction;
                }
            }
        }
        $voucher_save = AccountVoucher::create($account_voucher_inputs);
        $transactions_save = $voucher_save->account_voucher_transactions()->saveMany($transaction_array);

        return $voucher_save;


    }

    /**
     * Display the specified Sale.
     * GET|HEAD /sale/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Sale $sale */
        $sale = $this->saleRepository->find($id);

        if (empty($sale)) {
            return $this->sendError('Sale not found');
        }

        return $this->sendResponse($sale->toArray(), 'Sale retrieved successfully');
    }

    /**
     * Update the specified Sale in storage.
     * PUT/PATCH /sale/{id}
     *
     * @param int $id
     * @param UpdateSalesAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSalesAPIRequest $request)
    {
        $input = $request->all();

        /** @var Sale $sale */
        $sale = $this->saleRepository->find($id);

        if (empty($sale)) {
            return $this->sendError('Sale not found');
        }

        $sale = $this->saleRepository->update($input, $id);

        return $this->sendResponse($sale->toArray(), 'Sale updated successfully');
    } 

    public function sallingProducts(Request $request){  
        //$posts = Product::pluck('product_native_name','product_name','product_code','product_code'); //When using eloquent 
        //return response()->json($posts, 200);
        $limit = request('limit') ?? 10;
        $start_date = request('start_date') ?? '';
        $end_date = request('end_date') ?? '';
        if($request->type =='no-salling'){
            $data = self::sallingQuery()
               ->having('total', '<', 1)  
               ->orderBy('total','asc');
        } else if($request->type =='low-salling'){
            $data = self::sallingQuery() 
                ->when($start_date,function($query,$start_date,$end_date){ // if have start_date   
                    return $query->whereBetween('sale_items.created_at', [$start_date, $end_date]);
                }, function($query){ //  if haven't start date 
                    $query->whereBetween('sale_items.created_at',[(new Carbon)->subDays(30)->startOfDay()->toDateString(), (new Carbon)->now()->endOfDay()->toDateString()]);
                })
               ->orderBy('total','asc');  
        } else if($request->type =='best-salling'){
            $data = self::sallingQuery()
                ->when($start_date,function($query,$start_date,$end_date){ // if have start_date 
                    return $query->whereBetween('sale_items.created_at', [$start_date, $end_date]);
                }, function($query){ //  if have't start_date
                    $query->whereBetween('sale_items.created_at',[(new Carbon)->subDays(30)->startOfDay()->toDateString(), (new Carbon)->now()->endOfDay()->toDateString()]);
                }) 
               ->orderBy('total','desc');
        }  
        $data = $data->limit($limit)             
            ->get()->toArray();
        return response()->json($data, 200);
    }

    public function sallingQuery(){
        return SaleItem::
           rightJoin('products','products.id','=','sale_items.product_id')
           ->select('products.*',DB::raw("IFNULL(SUM(sale_items.quantity),0) as total") );  
    }


    public function saleHelper(Request $request){
        $data = array(
            'customers' => Customer::get()->toArray(), 
            'customer_groups' => CustomerGroup::get()->toArray(), 
            'districts' => District::get()->toArray(),
            'wallets' => MobileWallet::get()->toArray(),  
            'points_settings' => PointsSettings::first(), 
            'units' => Unit::get()->toArray(),
            'banks' => BankAccount::get()->toArray(),
        );
        return $this->sendResponse($data, 'Data retrieved successfully');
    }
    public function saleInfo(Request $request){   
        $searchColumns = $request->except(['type']);

        $type   = $request->get('type');

        $query = Sale::with(['payments','salesItems','customer','createdBy','salesDiscount']);
        $query->where(function($query) use ($searchColumns) {
            foreach($searchColumns as $key => $searchColumn){
                $query->where($key,$searchColumn);
            }
        });

        if($type == "return") {
            $query->where('return_type', 0);
        }
//        $data = $query->withSum('salesItems', 'discount')->get()->toArray();
        $data = $query->addSelect(['sales_items_sum_discount' => SaleItem::whereColumn('sale_id', 'sales.id')->selectRaw('sum(quantity * discount) as sales_items_sum_discount')])->get()->toArray();
        if(empty($data)) {
            return $this->sendError("Invoice not found!");
        }
        // auth('api')->user()
        //$invoice_number =  $request->invoice_number;
        //$data = Sale::InvoiceNumber($invoice_number)->with(['payments','salesItems'])->get()->toArray(); 
         
        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function collection(Request $request){
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $input = $request->all();  
        $sale = $this->saleRepository->find($input['id']); 
        if ($request->payments) {
            $dueAmount = ($sale->grand_total - $sale->paid_amount);  
            $paid_amount = ($dueAmount - $request->payments[0]['amount']); 

            if($paid_amount == 0){
                $status = 'paid';
                $paid_amount = $request->payments[0]['amount']; 
            } elseif ($paid_amount > 0){
                $status = 'partial';
                $paid_amount = $request->payments[0]['amount']; 
            } else {
                $status = 'paid';
                $paid_amount = $dueAmount; 
            }
            $salesData = array(
                'status' => $status,
                'paid_amount' => $paid_amount,
            );  
            $sale = $this->saleRepository->update($salesData, $input['id']);
            $payments = []; 
            $getPayments = $request->payments; 
            $payments[] = new PaymentCollection([
                'amount'        => $request->payments[0]['amount'],
                'card_reference_no'   => $request->payments[0]['card_reference_code'],
                'gift_card'     => $request->payments[0]['gift_card'],
                'paying_by'     => $request->payments[0]['paid_by'],
                'payment_note'  => $request->payments[0]['payment_note'],
                'wallet_id'     => $request->payments[0]['wallet_id'],
            ]); 
            $sale->payments()->saveMany($payments);

            $CustomerLedger = CustomerLedger::orderBy('id', 'DESC')->where('customer_id', $sale->customer_id)->first(); 
            if(empty($CustomerLedger)) {
                $customer_opening_balance = 0;
                $closing_balance = $paid_amount;
            }else{
                $customer_opening_balance = $CustomerLedger->closing_balance; 
                $closing_balance = ($customer_opening_balance + $paid_amount);
            }

            $customer_ledger_data = new CustomerLedger();
            $customer_ledger_data->customer_id = $sale->customer_id;
            $customer_ledger_data->transaction_type = 'collection';
            $customer_ledger_data->note = 'Due Sale Collection';
            $customer_ledger_data->debit_amount = 0;
            $customer_ledger_data->credit_amount = $paid_amount;
            $customer_ledger_data->opening_balance = $customer_opening_balance; 
            $customer_ledger_data->closing_balance = $closing_balance; 
            $customer_ledger_data->transaction_date = date("Y-m-d");
            $sale->customerLedger()->save($customer_ledger_data);

            /** For Sales Account Transaction  */
            // $cust_discount = 0;
            // $cust_group_discount = 0;
            // $order_discount = 0;

            // $total_discount = 0;
            // $total_vat_amount = 0;

             $sales_transaction_data = [
                 'fiscal_year_id'    => $fiscal_year->id,
                 'total_cash_amount' => $request->payments[0]['amount'],
             ];

            // Account Transaction
            $account_transaction = $this->collectionAccountTransaction($sales_transaction_data);
            //$sale->update(['voucher_id' => $account_transaction->id]);

            return $this->sendResponse($sale, 'Collection successfully');
        }else{
            return $this->sendError('Payment amount not found');
        }
        
    }


    public function collectionAccountTransaction($data=array())
    {
        $entry_type = EntryType::where('label', 'journal')->first();
        $voucher_code   = $this->returnVoucherCode('journal');
        $account_voucher_inputs  = [
            'vcode' => $voucher_code,
            'vtype_id'  => $entry_type->id,
            'vtype_value'   => 'auto voucher',
            'fiscal_year_id'    => $data['fiscal_year_id'],
            'vdate' => date("Y-m-d"),
            'global_note'   => 'Sales Amount Collection',
            'modified_item' => 0,
        ];

        // 1st Transaction
        $cash_ledger = $this->getLedgerData('ledger_code', '120701'); // dr assets
        $account_receivable_ledger = $this->getLedgerData('ledger_code', '120201'); // cr assets

        $transactions    = [
            // first Transaction
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'dr',
                'ledger_id' => $cash_ledger->id,
                'ledger_code' => $cash_ledger->ledger_code,
                'debit' => $data['total_cash_amount'],
                'credit'    => 0,
                'reference_id'  => null,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
            new AccountVoucherTransaction([
                'cost_center_id'    => 2,
                'vaccount_type'   => 'cr',
                'ledger_id' => $account_receivable_ledger->id,
                'ledger_code' => $account_receivable_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $data['total_cash_amount'],
                'reference_id'  => $cash_ledger->ledger_code,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),

        ];

        $transaction_array = [];
        foreach ($transactions as $transaction) {
            if(($transaction->debit > 0 || $transaction->credit > 0)) {
                $transaction_array[] = $transaction;
            }
        }
        $voucher_save = AccountVoucher::create($account_voucher_inputs);
        $voucher_save->account_voucher_transactions()->saveMany($transaction_array);

        return $voucher_save;


    }


    /**
     * Remove the specified Sale from storage.
     * DELETE /sale/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Sale $sale */
        $sale = $this->saleRepository->find($id);

        if (empty($sale)) {
            return $this->sendError('Sale not found');
        }

        $sale->delete();

        return $this->sendSuccess('Sale deleted successfully');
    }


    protected function getLedgerData($key, $value)
    {
        $ledger_data    = AccountLedger::where($key, $value)->first();

        return $ledger_data;
    }


    public function dueInvoices(Request $request)
    {  
        try {
            $customer_id = request()->has('customer_id') ? request()->get('customer_id') : null; 
            $customer = Customer::has('receivable_accounts')
                        ->when($customer_id, function($query) use ($customer_id) { 
                            $query->where('id', $customer_id); 
                        })->first();  
            if ($customer) { 
                $orders = Sale::with(['payments:id,sale_id,amount', 'customerLedger'])
                    ->when($customer_id, function($query) use ($customer) { 
                        $query->where('customer_id', $customer->id); 
                    }) 
                    ->select('sales.*', DB::raw('COALESCE((SELECT SUM(amount) FROM payment_collections WHERE payment_collections.sale_id = sales.id), 0) as total_payment'))
                    ->havingRaw('total_payment < grand_total')
                    ->get(); 
            }else{
                $orders = array();
            }

            $data = [ 
                'customer_id' => $customer_id, 
                'customers' => $customer,  
                'orders' => $orders,
            ];

            return $this->sendResponse($data, 'Due invoice list retrive successfully'); 
        }catch (\Throwable $th){
            return $this->sendError($th->getMessage()); 
        }
    }
}
