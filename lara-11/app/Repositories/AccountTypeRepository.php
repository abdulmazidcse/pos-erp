<?php

namespace App\Repositories;

use App\Models\AccountType;
use App\Repositories\BaseRepository;

/**
 * Class AccountTypeRepository
 * @package App\Repositories
 * @version July 24, 2022, 4:49 am UTC
*/

class AccountTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type_name'
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
        return AccountType::class;
    }
}
