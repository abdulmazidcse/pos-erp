<?php

namespace App\Repositories;

use App\Models\EntryType;
use App\Repositories\BaseRepository;

/**
 * Class EntryTypeRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:21 pm UTC
*/

class EntryTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'label'
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
        return EntryType::class;
    }
}
