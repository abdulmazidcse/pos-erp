<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
                'product_type' => $row[0],
                'product_name' => $row[1],
                'product_native_name' => $row[2],
                'product_code' => $row[3],
                'category_name' => $row[4],
                'sub_category_name' => $row[5],
                'brand_name' => $row[6],
                'cost_price' => $row[7],
                'depo_price' => $row[8],
                'mrp_price'  => $row[9],
                'tax_method' => $row[10],
                'alert_quantity' => $row[11],
                'short_description' => $row[12],
                'description' => $row[13],
                'is_expirable' => $row[14],
                'purchase_measuring_unit' => $row[15],
                'sales_measuring_unit' => $row[16],
        ]);
    }
}
