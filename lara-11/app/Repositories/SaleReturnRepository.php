<?php

namespace App\Repositories;

use App\Models\SaleReturn;
use App\Repositories\BaseRepository;

/**
 * Class SaleReturnRepository
 * @package App\Repositories
 * @version July 16, 2022, 11:26 am UTC
*/

class SaleReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sale_id',
        'return_type',
        'return_amount',
        'return_reason'
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
        return SaleReturn::class;
    }
}
