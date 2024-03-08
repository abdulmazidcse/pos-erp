<?php

namespace App\Http\Controllers\API;

use App\Exports\Export;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\InventorySummaryResource;
use App\Models\ProductCategory;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class InventoryReportController extends AppBaseController
{

    public function inventorySummaryReport(Request $request)
    {

        $columns = ['id', 'name', 'item_count', 'stock_quantity', 'stock_purchase_amount', 'stock_sale_amount'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');


        $outlet_id = $request->get('outlet_id');
        $category_id = $request->get('category_id');

        $product_categories = ProductCategory::has('parents')
            ->when($category_id, function ($query) use ($category_id) {
                return $query->where('id', $category_id);
            })->where('status', 1);


        $inventory_data = StockProduct::with(['product'])->where('stock_quantity', '>', 0)
                        ->when($category_id, function ($query) use ($category_id) {
                            return $query->where('category_id', $category_id);
                        })
                        ->when($outlet_id, function ($query) use ($outlet_id) {
                            return $query->where('outlet_id', $outlet_id);
                        })->get();


        $category_wise_data = [];
        if(count($inventory_data) > 0) {
            foreach ($inventory_data as $inventory) {
                if(!isset($category_wise_data[$inventory->category_id])) {
                    $category_wise_data[$inventory->category_id] = [
                        'stock_quantity'    => 0,
                        'stock_purchase_amount'    => 0,
                        'stock_sale_amount'    => 0,
                    ];
                }
                $category_wise_data[$inventory->category_id]['stock_quantity'] += $inventory->stock_quantity;
                $category_wise_data[$inventory->category_id]['stock_purchase_amount'] += $inventory->stock_quantity * $inventory->product->cost_price;
                $category_wise_data[$inventory->category_id]['stock_sale_amount'] += $inventory->stock_quantity * $inventory->product->mrp_price;

            }
        }


        //final array
        $final_array    = [];
        if(count($product_categories->get()) > 0) {
            $i = 0;
            foreach ($product_categories->get() as $category) {
                if(isset($category_wise_data[$category->id])) {
                    $i++;
                    $final_array[] = [
                        'SL'    => $i,
                        'id'    => $category->id,
                        'name'  => $category->name,
                        'item_count'    => collect($inventory_data)->where('category_id', $category->id)->count(),
                        'stock_quantity'    => $category_wise_data[$category->id]['stock_quantity'],
                        'stock_purchase_amount'    => $category_wise_data[$category->id]['stock_purchase_amount'],
                        'stock_sale_amount'    => $category_wise_data[$category->id]['stock_sale_amount'],

                    ];
                }
            }
        }

        if(strtolower($dir) == 'desc') {
            $sort_data = collect($final_array)->{"sortBy" . ucfirst($dir)}($columns[$column]);
        }else{
            $sort_data = collect($final_array)->sortBy($columns[$column]);
        }

        if ($searchValue) {
            $sort_data = $sort_data->filter(function ($item) use ($searchValue) {
                // Using LIKE operator for case-insensitive search
                return stripos($item['name'], $searchValue) !== false ||
                    stripos((string) $item['item_count'], $searchValue) !== false;
            });
        }
        $filtered_data = $sort_data->values()->all();

        $final_data = $this->paginate($filtered_data, $length, null, ['path' => route('inventorySummaryReport')]);

        $return_data    = [
            'data' => $final_data,
            'draw' => $request->input('draw'),
        ];
        return $this->sendResponse($return_data, 'Stock data retrieved successfully');

    }

    public function inventorySummaryReportExcelExport(Request $request){
        $response = $this->inventorySummaryReport($request);
        // Get the data array from the JSON response
        $data = $response->getData();

        $returnData = $data->data->data->data;

        $customHeadings = [ ['Inventory Summary Report'],[]];
        $columns = ['SL', 'name','item_count','stock_quantity','stock_purchase_amount','stock_sale_amount'];

        // Create an instance of the export class with the data
        $margeRangeOne = 'A1:F1';
        $margeRangeTwo = 'A2:F2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'inventory-details-report.xlsx');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

}