<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterOfCredit extends Model
{
    protected $fillable = [
        'lc_number', 'pi_id', 'bank_id', 'opening_date', 'expiry_date',
        'amount', 'margin_percentage', 'bank_charges', 'status', 'document_path'
    ];

    public function performaInvoice()
    {
        return $this->belongsTo(PerformaInvoice::class, 'pi_id');
    }

    public function costSheets()
    {
        return $this->hasMany(CostSheet::class, 'lc_id');
    }
}
