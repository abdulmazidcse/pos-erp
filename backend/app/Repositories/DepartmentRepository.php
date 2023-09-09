<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\BaseRepository;

/**
 * Class DepartmentRepository
 * @package App\Repositories
 * @version February 14, 2022, 3:32 am UTC
*/

class DepartmentRepository extends BaseRepository
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
        return Department::class;
    }
}
