<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Brand
 * @package App\Models
 * @version January 6, 2022, 7:28 am UTC
 *
 * @property string $name
 */
class Brand extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'brands';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name','description','website','logo','status','order', 'company_id', 'is_featured'
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
//        'name' => 'required'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
    
}
