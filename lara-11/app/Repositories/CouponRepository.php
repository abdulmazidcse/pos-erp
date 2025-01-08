<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Repositories\BaseRepository;

/**
 * Class CouponRepository
 * @package App\Repositories
 * @version April 26, 2022, 3:57 am UTC
*/

class CouponRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code',
        'name',
        'description',
        'uses',
        'max_uses',
        'max_uses_user',
        'type',
        'discount_amount',
        'is_fixed',
        'starts_at',
        'expires_at'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Coupon::class;
    }
}
