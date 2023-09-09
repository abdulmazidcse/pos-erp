<?php

namespace App\Repositories;

use App\Models\SupplierType;
use App\Repositories\BaseRepository;

/**
 * Class SupplierTypeRepository
 * @package App\Repositories
 * @version March 12, 2022, 2:56 am UTC
*/

class SupplierTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
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
        return SupplierType::class;
    }
}
