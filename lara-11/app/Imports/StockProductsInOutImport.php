<?php

namespace App\Imports;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\StockProductsLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockProductsInOutImport implements
    ToCollection,
    WithHeadingRow
{
    use Importable;

    public $in_out_data;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {


        $this->in_out_data = $rows;
        foreach($rows as $row) {

            $product = Product::where('product_name', $row['product_name'])->orWhere('product_code', $row['product_code'])->first();
            $outlet = Outlet::where('name', $row['outlet_name'])->first();

            if(!empty($product) && !empty($outlet)) {

                $new_in_stock_quantity = $row['in_stock_quantity'] ?? 0;
                $new_out_stock_quantity = $row['out_stock_quantity'] ?? 0;

                // Stock Adjustment With Expires Date
                if(!empty($row['expires_date'])) {
                    $expires_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expires_date']))->format("Y-m-d");
                    $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereDate('expires_date', $expires_date)->first();


                    $old_stock = $stock_product_data->stock_quantity ?? 0;
                    $log_insert_input = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_in_stock_quantity,
                        'stock_quantity'    => ($old_stock + $new_in_stock_quantity),
                        'out_stock_quantity'    => $new_out_stock_quantity,
                        'expires_date'  => $expires_date,
                        'stock_type'    => 'IO',
                        'note'          => $row['note'],
                        'user_id'   => auth()->user()->id ?? 1,
                    ];

                    if(!empty($stock_product_data)) {

                        $update_in_stock_quantity = ($stock_product_data->in_stock_quantity + $new_in_stock_quantity);
                        $update_stock_quantity = (($stock_product_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity);
                        $update_out_stock_quantity = ($stock_product_data->out_stock_quantity + $new_out_stock_quantity);

                        $update_inputs = [
                            'in_stock_quantity' => $update_in_stock_quantity,
                            'stock_quantity' => $update_stock_quantity,
                            'out_stock_quantity' => $update_out_stock_quantity,
                        ];

                        $stock_update = $stock_product_data->update($update_inputs);
                    }else{
                        $insert_inputs = [
                            'product_id'    => $product->id,
                            'outlet_id'     => $outlet->id,
                            'in_stock_quantity' => $new_in_stock_quantity,
                            'stock_quantity'    => ($new_in_stock_quantity - $new_out_stock_quantity),
                            'out_stock_quantity' => $new_out_stock_quantity,
                            'expires_date'  => $expires_date,
                        ];

                        $stock_save = StockProduct::create($insert_inputs);
                    }

                    $log_create = StockProductsLog::create($log_insert_input);

                }
                // Stock Adjustment Without Expires Date
                else{
                    $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereNull('expires_date')->first();

                    $old_stock = $stock_product_data->stock_quantity ?? 0;
                    $log_insert_input = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_in_stock_quantity,
                        'stock_quantity'    => ($old_stock + $new_in_stock_quantity),
                        'out_stock_quantity'    => $new_out_stock_quantity,
                        'stock_type'    => 'IO',
                        'note'          => $row['note'],
                        'user_id'   => auth()->user()->id ?? 1,
                    ];

                    if(!empty($stock_product_data)) {

                        $update_in_stock_quantity = ($stock_product_data->in_stock_quantity + $new_in_stock_quantity);
                        $update_stock_quantity = (($stock_product_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity);
                        $update_out_stock_quantity = ($stock_product_data->out_stock_quantity + $new_out_stock_quantity);

                        $update_inputs = [
                            'in_stock_quantity' => $update_in_stock_quantity,
                            'stock_quantity' => $update_stock_quantity,
                            'out_stock_quantity' => $update_out_stock_quantity,
                        ];

                        $stock_update = $stock_product_data->update($update_inputs);
                    }else{
                        $insert_inputs = [
                            'product_id'    => $product->id,
                            'outlet_id'     => $outlet->id,
                            'in_stock_quantity' => $new_in_stock_quantity,
                            'stock_quantity'    => ($new_in_stock_quantity - $new_out_stock_quantity),
                            'out_stock_quantity' => $new_out_stock_quantity,
                        ];

                        $stock_save = StockProduct::create($insert_inputs);
                    }

                    $log_create = StockProductsLog::create($log_insert_input);

                }

            }

        }
    }
}
