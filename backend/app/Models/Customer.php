<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Customer
 * @package App\Models
 * @version February 26, 2022, 4:26 am UTC
 *
 * @property string $name
 */
class Customer extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'customers';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'customer_code',
        'emp_code',
        'customer_group_id',
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'dob',
        'district_id',
        'area_id',
        'postal_code',
        'discount_percent',
        'wholesale_customer',
        'sale_without_vat',
        'credit_customer',
        'store_customer',
        'receivable_ledger_id',
        'status'
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

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id', 'id');
    }

    public function receivable_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'receivable_ledger_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
    public function totalPoints()
    {
        return $this->hasMany(UsersPoints::class, 'user_id', 'id')->where('type','insert');
    }
    public function totalRedeem()
    {
        return $this->hasMany(UsersPoints::class, 'user_id', 'id')->where('type','redeem');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
    
}
