<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Supplier
 * @package App\Models
 * @version February 27, 2022, 4:36 am UTC
 *
 * @property string $name
 */
class Supplier extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'suppliers';
    

    protected $dates = ['deleted_at'];



//    public $fillable = [
//        'name'
//    ];

    protected $guarded  = [];

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


    // Relations
    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function supplier_types()
    {
        return $this->belongsTo(SupplierType::class, 'type_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_suppliers', 'supplier_id', 'product_id');
    }

    public function supplier_ledgers()
    {
        return $this->hasMany(SupplierLedger::class, 'supplier_id', 'id');
    }

    public function payable_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'payable_ledger_id', 'id');
    }

    public function discount_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'discount_ledger_id', 'id');
    }

    public function advance_accounts()
    {
        return $this->belongsTo(AccountLedger::class, 'advance_ledger_id', 'id');
    }

    
}
