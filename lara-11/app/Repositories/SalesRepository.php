<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\BaseRepository;

/**
 * Class SalesRepository
 * @package App\Repositories
 * @version April 21, 2022, 6:52 am UTC
*/

class SalesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'customer_name',
        'total_amount',
        'collection_amount',
        'return_amount'
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
        return Sale::class;
    }
}
