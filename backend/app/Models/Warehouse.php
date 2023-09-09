<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Warehouse
 * @package App\Models
 * @version February 23, 2022, 6:17 am UTC
 *
 * @property string $name
 */
class Warehouse extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'warehouses';
    

    protected $dates = ['deleted_at'];



    public $fillable = ['name','company_id','contact_person_name','warehouse_number','address'];

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

    public function company(){
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    
}
