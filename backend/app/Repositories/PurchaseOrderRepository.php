<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseOrderRepository
 * @package App\Repositories
 * @version March 2, 2022, 4:07 am UTC
*/

class PurchaseOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'reference_no'
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
        return PurchaseOrder::class;
    }
}
