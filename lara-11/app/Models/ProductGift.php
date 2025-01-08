<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGift extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates = ['deleted_at'];


    protected $fillable = [
        'purchase_receive_details_id',
        'direct_pr_details_id',
        'product_id',
        'gift_name',
        'gift_quantity'
    ];
}
