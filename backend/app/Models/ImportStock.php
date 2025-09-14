<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportStock extends Model
{
    public $table = 'import_stocks';

    public $fillable = [
        'vendor_id'
    ];

    protected $casts = [
        'vendor_id' => 'integer'
    ];

    public static array $rules = [
        
    ];

    
}
