<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Department
 * @package App\Models
 * @version February 14, 2022, 3:32 am UTC
 *
 * @property string $name
 */
class Department extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'departments';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name', 'company_id', 'status'
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

    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
    
}
