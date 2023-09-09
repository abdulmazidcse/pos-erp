<?php

namespace App\Repositories;

use App\Models\AccountClass;
use App\Repositories\BaseRepository;

/**
 * Class AccountClassRepository
 * @package App\Repositories
 * @version August 2, 2022, 8:58 am UTC
*/

class AccountClassRepository extends BaseRepository
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
        return AccountClass::class;
    }
}
