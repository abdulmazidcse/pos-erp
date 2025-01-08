<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseReturn extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = "purchase_returns";

    protected $guarded = [];


    //Relations
    public function purchase_return_details()
    {
        return $this->hasMany(PurchaseReturnDetail::class, 'purchase_return_id', 'id');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

    public function warehouses()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //Scope For Purchase Return Data
    public function scopeFiltered(Builder $builder)
    {
        $columns = ['id','reference_no', 'return_date', 'supplier_name', 'total_return_quantity', 'total_return_amount'];
        $column = request('column') ?? null;
//        $dir = request('dir');
//        $searchValue = request('search');


//        $search = request('search') ?? null;
//        $searchColumns = request('searchColumns') ?? null;
//        $sort   = request('sort') ?? null;
//        $sortBy = request('sortBy') ?? null;
//        $sortColumns = request('sortColumns') ?? null;

        $search = request('search') ?? null;
        $searchColumns = $columns;
        $sort   = request('dir') ?? null;
        $sortBy = $columns[$column] ?? null;
        $sortColumns = request('sortColumns') ?? null;

        $query  = $builder->select(
            $this->table.'.id',
            $this->table.'.reference_no',
            $this->table.'.supplier_id',
            'suppliers.name as supplier_name',
            $this->table.'.outlet_id',
            'outlets.name as outlet_name',
            $this->table.'.warehouse_id',
            'warehouses.name as warehouse_name',
            $this->table.'.total_return_quantity',
            $this->table.'.total_return_amount',
            $this->table.'.return_date',
            $this->table.'.note',
            $this->table.'.status',
            $this->table.'.user_id',
            'users.name as user_name'
        )
        ->leftJoin('suppliers', 'suppliers.id', '=', $this->table.'.supplier_id')
        ->leftJoin('outlets', 'outlets.id', '=', $this->table.'.outlet_id')
        ->leftJoin('warehouses', 'warehouses.id', '=', $this->table.'.warehouse_id')
        ->leftJoin('users', 'users.id', '=', $this->table.'.user_id');

        // For Search
        if ($search && Str::length($search) > 0) {
            $listSearch = Str::of($search)->split('/[\s,]+/')->toArray();
            $search = count($listSearch) > 1 ? implode("%", $listSearch) : "%{$search}%";
//            $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();
            $query->where(function($query) use ($search, $searchColumns) {
                foreach($searchColumns as $searchColumn){
                    if($searchColumn == "supplier_name") {
                        $query->orWhereRaw("suppliers.name LIKE '{$search}'");
                    }else {
                        $query->orWhereRaw("{$this->table}.{$searchColumn} LIKE '{$search}'");
                    }
                }
            });
        }

//        $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();
//        if(collect($sortColumns)->contains($sortBy) &&  collect(['ASC', 'DESC'])->contains($sort)){
            $query->orderBy("{$this->table}.{$sortBy}", $sort);
//        }

        return $query;
    }


}
