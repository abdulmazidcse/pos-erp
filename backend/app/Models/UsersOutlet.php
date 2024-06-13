<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UserOutlet
 * @package App\Models
 * @version February 23, 2022, 6:17 am UTC
 *
 * @property string $name
 */
class UsersOutlet extends Model
{ 
    use SoftDeletes;

    use HasFactory; 
    
    
    public $fillable = ['user_id','outlet_id'];
    
    public $timestamps = false;
    

    public function user(){
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
    public function outlet(){
        return $this->belongsTo(Outlet::class, 'id', 'outlet_id');
    }

    
}
