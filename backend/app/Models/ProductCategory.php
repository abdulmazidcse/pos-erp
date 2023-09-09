<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductCategory
 * @package App\Models
 * @version January 6, 2022, 6:26 am UTC
 *
 * @property string $name
 */
class ProductCategory extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'product_categories';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'code',
        'parent_id',
        'description',
        'status',
        'order',
        'image',
        'company_id',
        'discount',
        'is_featured'
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

    public function childs()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function parents()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function scopeParent($query){
        return $query->where('parent_id',0);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    
}
