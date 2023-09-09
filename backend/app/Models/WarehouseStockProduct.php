<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WarehouseStockProduct extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'in_stock_quantity',
        'in_stock_weight',
        'stock_quantity',
        'stock_weight',
        'out_stock_quantity',
        'out_stock_weight',
        'expires_date'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->with(['purchase_unit','category','sub_category']);
    }

    public function warehouses()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function scopeFiltered(Builder $builder) {

        $columns = ['id', 'products.product_name', 'warehouses.name', 'products.product_code', 'units.unit_code', 'expires_date', 'in_stock_quantity', 'in_stock_weight', 'out_stock_quantity', 'out_stock_weight', 'stock_quantity', 'stock_weight'];

        $search = request('search') ?? null;
        $searchColumns = $columns ?? null;

        $sort = request('dir') ?? null;
        $sortBy = request('sortKey') ?? null;
        $sortColumns = $columns ?? null;

        $warehouse_id = request('warehouse_id');
        $category_id = request('category_id');
        $sub_category_id = request('sub_category_id');
        $from_date  = request('start_date');
        $to_date    = request('end_date');
        // format

        $warehouse_stock_products = $builder->select(
            'warehouse_stock_products.id AS id',
            'warehouse_stock_products.product_id AS product_id',
            'warehouse_stock_products.warehouse_id AS warehouse_id',
            'warehouses.name AS warehouse_name',
            'products.product_name AS product_name',
            'products.product_code AS product_code',
            'products.category_id AS category_id',
            'products.sub_category_id AS sub_category_id',
            'products.purchase_measuring_unit AS product_purchase_unit_id',
            'units.unit_code as punit',
            'warehouse_stock_products.expires_date AS expires_date',
            'warehouse_stock_products.in_stock_quantity AS in_stock_quantity',
            'warehouse_stock_products.in_stock_weight AS in_stock_weight',
            'warehouse_stock_products.out_stock_quantity AS out_stock_quantity',
            'warehouse_stock_products.out_stock_weight AS out_stock_weight',
            'warehouse_stock_products.stock_quantity AS stock_quantity',
            'warehouse_stock_products.stock_weight AS stock_weight'

        )
            ->join('warehouses', 'warehouses.id', 'warehouse_stock_products.warehouse_id')
            ->join('products', 'products.id', 'warehouse_stock_products.product_id')
            ->join('units', 'units.id', 'products.purchase_measuring_unit');

        if ($search && Str::length($search) > 0) {
            $listSearch = Str::of($search)->split('/[\s,]+/')->toArray();
            $search = count($listSearch) > 1 ? implode("%", $listSearch) : "%{$search}%";

//            $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();

            $warehouse_stock_products->where(function($query) use ($search, $searchColumns) {
                foreach($searchColumns as $searchColumn){
                    $sort_explode = explode('.', $searchColumn);
                    if(count($sort_explode) > 1){
                        $table_column = $searchColumn;
                    }else{
                        $table_column = "warehouse_stock_products.{$searchColumn}";
                    }
                    $query->orWhereRaw("{$table_column} LIKE '{$search}'");
                }
            });
        }

        if(isset($warehouse_id)) {
            $warehouse_stock_products->where('warehouse_stock_products.warehouse_id', $warehouse_id);
        }

        if(isset($category_id)) {
            $warehouse_stock_products->where('products.category_id', $category_id);
        }

        if(isset($sub_category_id)) {
            $warehouse_stock_products->where('products.sub_category_id', $sub_category_id);
        }

        if(isset($from_date)) {
            $warehouse_stock_products->whereDate('warehouse_stock_products.expires_date', '>=', $from_date);
        }

        if(isset($to_date)) {
            $warehouse_stock_products->whereDate('warehouse_stock_products.expires_date', '<=', $to_date);
        }




//        $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();

        if(collect($sortColumns)->contains($sortBy) &&  collect(['ASC', 'DESC'])->contains($sort)){

            $sort_explode = explode('.', $sortBy);
            if(count($sort_explode) > 1){
                $table_column = $sortBy;
            }else{
                $table_column = "warehouse_stock_products.{$sortBy}";
            }
            $warehouse_stock_products->orderBy($table_column, $sort);
        }

        return $warehouse_stock_products;
    }
}
