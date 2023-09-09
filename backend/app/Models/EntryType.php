<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class EntryType
 * @package App\Models
 * @version July 27, 2022, 12:21 pm UTC
 *
 * @property string $label
 */
class EntryType extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'entry_types';
    

    protected $dates = ['deleted_at'];

    protected $appends = ['number_string','restriction_string'];

    public $fillable = [
        'label',
        'name',
        'description',
        'numbering',
        'prefix',
        'suffix',
        'zero_padding',
        'restrictions'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        //'label' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // get custom attribute
    public function getNumberStringAttribute()
    {
        switch ($this->numbering) {
            case 1:
                $attr = 'Auto';
                break;
            case 2:
                $attr = 'Manual(required)';
                break;
            case 3:
                $attr = 'Manual(optional)';
                break;
            default:
                $attr = 'Auto';
                break;
        }

        return $attr;
    }

    public function getRestrictionStringAttribute()
    {
        switch ($this->restrictions) {
            case 1:
                $attr = 'Unrestricted';
                break;
            case 2:
                $attr = 'Atleast one Bank or Cash account must be present on Debit side';
                break;
            case 3:
                $attr = 'Atleast one Bank or Cash account must be present on Credit side';
                break;
            case 4:
                $attr = 'Only Bank or Cash account can be present on both Debit and Credit side';
                break;
            case 5:
                $attr = 'Only NON Bank or Cash account can be present on both Debit and Credit side';
                break;
            default:
                $attr = 'Unrestricted';
                break;
        }

        return $attr;
    }

    
}
