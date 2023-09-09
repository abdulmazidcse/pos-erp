<?php

namespace App\Repositories;

use App\Models\StoreRequisition;
use App\Repositories\BaseRepository;

/**
 * Class StoreRequisitionRepository
 * @package App\Repositories
 * @version March 14, 2022, 3:57 am UTC
*/

class StoreRequisitionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'requisition_no'
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
        return StoreRequisition::class;
    }
}
