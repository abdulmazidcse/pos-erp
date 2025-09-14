<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStock extends Model
{
    protected $fillable = [
        'pi_id', 'product_id', 'quantity', 'unit_cost', 'is_serialized', 'status'
    ];

    public function performaInvoice()
    {
        return $this->belongsTo(PerformaInvoice::class, 'pi_id');
    }

    public function importStocks()
    {
        return $this->hasMany(ImportStock::class, 'pipeline_stock_id');
    }
}
