<?php

namespace App\Repositories;

use App\Models\CustomerGroup;
use App\Repositories\BaseRepository;

/**
 * Class CustomerGroupRepository
 * @package App\Repositories
 * @version April 7, 2022, 8:54 am UTC
*/

class CustomerGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'value'
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
        return CustomerGroup::class;
    }
}
