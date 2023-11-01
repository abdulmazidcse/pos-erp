<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [
//        'name',
//        'email',
//        'phone',
//        'company_id',
//        'profile_image',
//    ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    // Relation
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'users_outlets');
    }


    public function getProfileImageAttribute($date)
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            return $date ? asset('public/uploads/users/'.$date) : '';
        }else {
            return $date ? asset('uploads/users/' . $date) : '';
        }
    } 

    /** For Permission */
    //    public static function roleHasPermissions($role, $permissions) {
    //        $hasPermission = true;
    //
    //        foreach ($permissions as $permission) {
    //
    //            if (!$role->hasPermissionTo($permission->name)) {
    //                $hasPermission = false;
    //                return $hasPermission;
    //            }
    //
    //        }
    //
    //        return $hasPermission;
    //    }

    public function getPermissionAttribute() {
        return $this->getAllPermissions();
    } 

    // Get the user's most recent order
    public function latestOrder(){
        return $this->hasOne(Sale::class,'created_by','id')->latestOfMany();
    }

    // Get the user's most oldest order
    public function oldestOrder(){
        return $this->hasOne(Sale::class,'created_by','id')->oldestOfMany();
    } 

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
