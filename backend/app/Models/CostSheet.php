<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostSheet extends Model
{
    protected $fillable = [
        'lc_id', 'product_id', 'cost_elements', 'unit_cost', 'total_cost'
    ];

    protected $casts = [
        'cost_elements' => 'array',
    ];

    public function letterOfCredit()
    {
        return $this->belongsTo(LetterOfCredit::class, 'lc_id');
    }
}
