<?php

namespace App\Repositories;

use App\Models\AccountGroup;
use App\Repositories\BaseRepository;

/**
 * Class AccountGroupRepository
 * @package App\Repositories
 * @version July 20, 2022, 6:13 am UTC
*/

class AccountGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id'
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
        return AccountGroup::class;
    }
}
