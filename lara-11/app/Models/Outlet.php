<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Outlet
 * @package App\Models
 * @version February 7, 2022, 8:52 am UTC
 *
 * @property string $name
 */
class Outlet extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'outlets';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'company_id',
        'name',  
        'contact_person_name',
        'outlet_number',
        'district_id',
        'area_id',
        'police_station',
        'road_no',
        'plot_no',
        'latitude',
        'longitude',
        'status'
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


    public function company(){
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function districts(){
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function areas(){
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_outlets');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    
}
