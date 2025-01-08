<?php

namespace App\Repositories;

use App\Models\HoldSaleDiscount;
use App\Repositories\BaseRepository;

/**
 * Class HoldSaleDiscountRepository
 * @package App\Repositories
 * @version June 7, 2022, 5:36 pm UTC
*/

class HoldSaleDiscountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'hold_sale_id',
        'discount_value'
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
        return HoldSaleDiscount::class;
    }
}
