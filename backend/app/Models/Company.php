<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Company
 * @package App\Models
 * @version February 7, 2022, 8:45 am UTC
 *
 * @property string $name
 * @property string $address
 */
class Company extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'companies';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'logo',
        'address',
        'contact_person_name',
        'contact_person_number',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'address' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class, 'company_id', 'id');
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class, 'company_id', 'id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class, 'company_id', 'id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'company_id', 'id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'company_id', 'id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function fiscal_years()
    {
        return $this->hasMany(FiscalYear::class, 'company_id', 'id');
    }



    
}
