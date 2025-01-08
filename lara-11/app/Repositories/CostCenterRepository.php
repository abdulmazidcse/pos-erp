<?php

namespace App\Repositories;

use App\Models\CostCenter;
use App\Repositories\BaseRepository;

/**
 * Class CostCenterRepository
 * @package App\Repositories
 * @version July 31, 2022, 6:19 am UTC
*/

class CostCenterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id'
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
        return CostCenter::class;
    }
}
