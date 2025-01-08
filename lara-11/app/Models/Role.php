<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

//    public $table = 'roles';
//
//    public $fillable = [
//        'name', 'slug'
//    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];


//    public function permissions() {
//
//        return $this->belongsToMany(Permission::class,'roles_permissions');
//
//    }
//
//    public function users() {
//
//        return $this->belongsToMany(User::class,'users_roles');
//
//    }

}
