<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHoldSaleAPIRequest;
use App\Http\Requests\API\UpdateHoldSaleAPIRequest;
use App\Models\HoldSale;
use App\Models\Product;
use App\Models\MobileWallet;
use App\Models\Customer;
use App\Models\HoldSaleItem;
use App\Repositories\HoldSaleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use DB;
use Carbon\Carbon;  
use Auth;

/**
 * Class HoldSaleController
 * @package App\Http\Controllers\API
 */

class HoldSaleAPIController extends AppBaseController
{
    /** @var  HoldSaleRepository */
    private $HoldSaleRepository;

    public function __construct(HoldSaleRepository $holdSaleRepo)
    {
        $this->HoldSaleRepository = $holdSaleRepo;
    }

    /**
     * Display a listing of the HoldSale.
     * GET|HEAD /holdSales
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $holdSales = $this->HoldSaleRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($holdSales->toArray(), 'Hold Sales retrieved successfully');
    }


    public function list(Request $request)
    { 

        $columns = ['id','created_at', 'invoice_number', 'total_amount', 'customer_name', 'collection_amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = HoldSale::select('id','created_at', 'invoice_number', 'total_amount', 'customer_name', 'collection_amount')->orderBy($columns[$column], $dir);

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
        return $this->sendResponse($return_data, 'Hold Sale retrieved successfully');
    }

    /**
     * Store a newly created HoldSale in storage.
     * POST /holdSales
     *
     * @param CreateHoldSaleAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateHoldSaleAPIRequest $request)
    {
        $this->validate($request, [
            'customer_id'  => 'required', 
        ]); 

        $input = $request->all(); 
        $items = [];
        $update_stock = [];
        if ($request->items) {
            $getItems = $request->items;  
            foreach ($getItems as $key => $value) {  
                $product = Product::find($value['product_id']);   
                $items[] = new HoldSaleItem([
                    'product_id' => $value['product_id'],
                    'product_stock_id' => $value['product_stock_id'],
                    'quantity' => $value['quantity'],
                    'discount' => $value['discount'], 
                    'vat' => $value['tax'],
                    'vat_id' => $product->product_tax, 
                    'mrp_price' => $product->mrp_price,
                    'cost_price' => $product->cost_price 
                ]);
            } 
        } 
        $customer = Customer::find($request->customer_id);   
        $id=\DB::select("show table status where name='hold_sales'; ");
        $next_sale_id=$id[0]->Auto_increment; 
        $outlet_id = 1;
        if ($request->items) {
            $salesData = array(
                'customer_id' => $request->customer_id,
                'invoice_number' => 'P'.sprintf('%03d',$outlet_id).date('y').sprintf('%06d',$next_sale_id),
                'customer_name' => $customer->name,
                'total_amount' => $request->total_amount,
                'grand_total' => $request->grand_total,
                'collection_amount' => $request->total_collect_amount,
                'paid_amount' => $request->paid_amount,
                'return_amount' => $request->return_amount,
                'sale_type' => 'pos',  
                'order_discount' => $request->order_discount,
                'order_discount_value' => $request->order_discount_value,
                'order_vat' => $request->order_vat, 
                'order_items_vat' => $request->order_items_vat, 
                'outlet_id' => $outlet_id, 
                'status' => $request->status,
                'sale_note' => $request->sale_note,
                'staff_note' => $request->staff_note, 
            ); 
        } 
        \DB::beginTransaction();

        try{ 
            $sale = $this->HoldSaleRepository->create($salesData); 
            $sale->salesItems()->saveMany($items);  
            \DB::commit();
            return $this->sendResponse($sale->toArray(), 'Sale saved successfully');

        }catch(\Exception $e){
            \DB::rollback();
            return $this->sendError($e->getMessage());
        } 

        return $this->sendResponse($sale->toArray(), 'somthing is wrong');
    }
    public function store1(CreateHoldSaleAPIRequest $request)
    {
        $input = $request->all();

        $holdSale = $this->HoldSaleRepository->create($input);

        return $this->sendResponse($holdSale->toArray(), 'Hold Sale saved successfully');
    }

    /**
     * Display the specified HoldSale.
     * GET|HEAD /holdSales/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var HoldSale $holdSale */
        $holdSale = $this->HoldSaleRepository->find($id);

        if (empty($holdSale)) {
            return $this->sendError('Hold Sale not found');
        }

        return $this->sendResponse($holdSale->toArray(), 'Hold Sale retrieved successfully');
    }

    /**
     * Update the specified HoldSale in storage.
     * PUT/PATCH /holdSales/{id}
     *
     * @param int $id
     * @param UpdateHoldSaleAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHoldSaleAPIRequest $request)
    {
        $input = $request->all();

        /** @var HoldSale $holdSale */
        $holdSale = $this->HoldSaleRepository->find($id);

        if (empty($holdSale)) {
            return $this->sendError('Hold Sale not found');
        }

        $holdSale = $this->HoldSaleRepository->update($input, $id);

        return $this->sendResponse($holdSale->toArray(), 'HoldSale updated successfully');
    }

    /**
     * Remove the specified HoldSale from storage.
     * DELETE /holdSales/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var HoldSale $holdSale */
        $holdSale = $this->HoldSaleRepository->find($id);

        if (empty($holdSale)) {
            return $this->sendError('Hold Sale not found');
        }

        $holdSale->delete();

        return $this->sendSuccess('Hold Sale deleted successfully');
    }
}
