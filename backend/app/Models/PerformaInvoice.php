<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformaInvoice extends Model
{
    protected $fillable = [
        'pi_number', 'vendor_id', 'issue_date', 'expiry_date',
        'shipment_type', 'total_amount_usd', 'total_amount_local',
        'currency_code', 'exchange_rate', 'status', 'is_part_shipment'
    ];

    public function letterOfCredits()
    {
        return $this->hasMany(LetterOfCredit::class, 'pi_id');
    }

    public function pipelineStocks()
    {
        return $this->hasMany(PipelineStock::class, 'pi_id');
    }
}
