<?php

namespace App\Models;
use Illuminate\Support\Str;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
Use Carbon\Carbon;

/**
 * Class HoldSale
 * @package App\Models
 * @version June 7, 2022, 5:38 pm UTC
 *
 * @property integer $ss_id
 */
class HoldSale extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'hold_sales';
    

    protected $dates = ['deleted_at']; 
    protected $dateFormat = 'U';
    protected $guarded = [];
    public $timestamps = true;


    // public $fillable = [
    //     'invoice_number'
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
//        'created_at' => "datetime:Y-m-d\TH:iPZ",
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function payments()
    {
        return $this->hasMany(PaymentCollection::class, 'hold_sale_id', 'id');
    }
    public function salesItems()
    {
        return $this->hasMany(HoldSaleItem::class, 'hold_sale_id', 'id');
    }
    public function salesItemCount()
    {
        return $this->hasMany(HoldSaleItem::class, 'hold_sale_id', 'id')->count();
    }
    public function point() {
        return $this->hasOne(UsersPoints::class, 'hold_sale_id','id');
    }
    public function customerLedger() {
        return $this->hasOne(CustomerLedger::class, 'hold_sale_id','id');
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
        return Carbon::parse($date)->format('d M Y'); 
    }

    
}
