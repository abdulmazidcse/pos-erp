<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreRequisitionDetail extends Model
{
    use SoftDeletes;

    use HasFactory;


    protected $guarded = [];


    // Relations
    public function store_requisitions()
    {
        return $this->belongsTo(StoreRequisition::class, 'store_requisition_id', 'id');
    }
}
