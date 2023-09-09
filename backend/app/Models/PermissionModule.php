<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PermissionModule
 * @package App\Models
 * @version February 20, 2022, 7:10 am UTC
 *
 * @property string $name
 */
class PermissionModule extends Model
{
    use HasFactory;

    public $table = 'permission_modules';



    public $fillable = [
        'name', 'slug', 'parent_id', 'icon_name', 'menu_order', 'is_children', 'is_action_menu','is_multiple_action','total_actions'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // Relations
    public function parents()
    {
        return $this->belongsTo(PermissionModule::class, 'parent_id', 'id');
    }

    public function sub_modules()
    {
        return $this->hasMany(PermissionModule::class, 'parent_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }



    
}
