<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class FiscalYear
 * @package App\Models
 * @version July 30, 2022, 6:56 am UTC
 *
 * @property string $label
 */
class FiscalYear extends Model
{

    use SoftDeletes;

    use HasFactory;

    public $table = 'fiscal_years';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'label',
        'start_date',
        'end_date',
        'company_id',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'label' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // Relations
    public function companies() {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }


    
}
