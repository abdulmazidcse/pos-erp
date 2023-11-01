<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * Class District
 * @package App\Models
 * @version February 16, 2022, 10:07 am UTC
 *
 * @property string $name
 */
class District extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'districts';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'division_id','name','bn_name','status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function areas()
    {
        return $this->hasMany(Area::class, 'district_id', 'id');
    }

    public function scopeFiltered(Builder $builder) {
        $search = request('search') ?? null;
        $searchColumns = request('searchColumns') ?? null;

        $sort = request('sort') ?? null;
        $sortBy = request('sortBy') ?? null;
        $sortColumns = request('sortColumns') ?? null;

        // format

        $districts = $builder->select(
            'districts.id AS id',
            'districts.name AS name',
            'districts.bn_name AS bn_name'
        );

        if ($search && Str::length($search) > 0) {
            $listSearch = Str::of($search)->split('/[\s,]+/')->toArray();
            $search = count($listSearch) > 1 ? implode("%", $listSearch) : "%{$search}%";

            $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();

            $districts->where(function($query) use ($search, $searchColumns) {
                foreach($searchColumns as $searchColumn){
                    $query->orWhereRaw("districts.{$searchColumn} LIKE '{$search}'");
                }
            });
        }


        $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();

        if(collect($sortColumns)->contains($sortBy) &&  collect(['ASC', 'DESC'])->contains($sort)){
            $districts->orderBy("users.{$sortBy}", $sort);
        }

        return $districts;
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }


    
}
