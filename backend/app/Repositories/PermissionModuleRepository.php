<?php

namespace App\Repositories;

use App\Models\PermissionModule;
use App\Repositories\BaseRepository;

/**
 * Class PermissionModuleRepository
 * @package App\Repositories
 * @version February 20, 2022, 7:10 am UTC
*/

class PermissionModuleRepository extends BaseRepository
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
        return PermissionModule::class;
    }
}
