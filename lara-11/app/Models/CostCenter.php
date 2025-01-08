<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CostCenter
 * @package App\Models
 * @version July 31, 2022, 6:19 am UTC
 *
 * @property integer $company_id
 */
class CostCenter extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'cost_centers';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'company_id',
        'outlet_id',
        'center_name',
        'note',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

    
}
