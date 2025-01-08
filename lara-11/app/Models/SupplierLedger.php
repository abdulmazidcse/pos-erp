<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierLedger extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates    = ['deleted_at'];

    protected $fillable = [
        'supplier_id',
        'purchase_receive_id',
        'voucher_id',
        'transaction_type',
        'opening_balance',
        'purchase_receive_amount',
        'payment_amount',
        'return_amount',
        'discount_amount',
        'closing_balance',
        'transaction_date',
    ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
