<?php

namespace App\Models;
use Illuminate\Support\Str;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
Use Carbon\Carbon;
/**
 * Class Sales
 * @package App\Models
 * @version April 21, 2022, 6:52 am UTC
 *
 * @property integer $customer_id
 * @property string $customer_name
 * @property number $total_amount
 * @property number $collection_amount
 * @property number $return_amount
 */
class Sale extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'sales';
        
    protected $guarded = [];
    // public $fillable = [
    //     'customer_id',
    //     'customer_name',
    //     'total_amount',
    //     'collection_amount',
    //     'return_amount'
    // ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customer_id' => 'integer',
        'customer_name' => 'string',
//        'total_amount' => 'double',
//        'collection_amount' => 'double',
//        'return_amount' => 'double',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function payments()
    {
        return $this->hasMany(PaymentCollection::class, 'sale_id', 'id');
    }
    public function salesItems()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id')->with('products');
    }
    public function salesDiscounts()
    {
        return $this->hasMany(SalesDiscount::class, 'sale_id', 'id');
    }
    public function salesItemCount()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id')->count();
    }
    public function point() {
        return $this->hasOne(UsersPoints::class, 'sale_id','id');
    }
    public function customerLedger() {
        return $this->hasOne(CustomerLedger::class, 'sale_id','id');
    }
    public function customer() {
        return $this->hasOne(Customer::class, 'id','customer_id');
    }

    public function outlets() {
        return $this->belongsTo(Outlet::class, 'outlet_id','id');
    }
    public function createdBy() {
        return $this->hasOne(User::class, 'id','created_by');
    }

    public function scopeFiltered(Builder $builder) {
        $search = request('search') ?? null;
        $searchColumns = request('searchColumns') ?? null;
        $sort = request('sort') ?? null;
        $sortBy = request('sortBy') ?? null;
        $sortColumns = request('sortColumns') ?? null;
        
        $data = $builder->select(
            $this->table.'.id AS id',
            $this->table.'.invoice_number AS invoice_number',
            $this->table.'.created_at AS created_at',
            $this->table.'.customer_name AS customer_name', 
            $this->table.'.total_amount AS total_amount',
            $this->table.'.collection_amount AS collection_amount'
        );

        if ($search && Str::length($search) > 0) {
            $listSearch = Str::of($search)->split('/[\s,]+/')->toArray();
            $search = count($listSearch) > 1 ? implode("%", $listSearch) : "%{$search}%";
            $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();
            $data->where(function($query) use ($search, $searchColumns) {
                foreach($searchColumns as $searchColumn){
                    $query->orWhereRaw("{$this->table}.{$searchColumn} LIKE '{$search}'");
                }
            });
        }
        $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();
        if(collect($sortColumns)->contains($sortBy) &&  collect(['ASC', 'DESC'])->contains($sort)){
            $data->orderBy("{$this->table}.{$sortBy}", $sort);
        }
        return $data;
    }
    public function getCreatedAtAttribute($date)
    { 
        return date('d M Y',strtotime($date)); 
        //return Carbon::parse($date)->format('d M Y'); 
    }

    public function scopeInvoiceNumber($query, $param){
        return $query->where('invoice_number', $param);
    }

    public function salesDiscount()
    {
        return $this->hasManyThrough(SalesDiscount::class, SaleItem::class);
    }
    
    // public function points(){
    //     return $this->belongsToMany(UsersPoints::class, 'sale_id', 'id');
    // }
    // public function point(){
    //     return $this->hasMany(UsersPoints::class, 'sale_id', 'id');
    // }

    
}
