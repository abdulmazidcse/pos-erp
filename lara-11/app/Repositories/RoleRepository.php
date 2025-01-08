<?php

namespace App\Repositories;


use App\Repositories\BaseRepository;
use App\Models\Role;

/**
 * Class RoleRepository
 * @package App\Repositories
 * @version February 7, 2022, 3:51 am UTC
*/

class RoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
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
        return Role::class;
    }
}
