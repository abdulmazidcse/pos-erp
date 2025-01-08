<?php

namespace App\Repositories;

use App\Models\SalesDiscount;
use App\Repositories\BaseRepository;

/**
 * Class SalesDiscountRepository
 * @package App\Repositories
 * @version June 7, 2022, 5:12 pm UTC
*/

class SalesDiscountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sale_id',
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
        return SalesDiscount::class;
    }
}
