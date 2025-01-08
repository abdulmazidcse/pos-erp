<?php

namespace App\Repositories;

use App\Models\Unit;
use App\Repositories\BaseRepository;

/**
 * Class UnitRepository
 * @package App\Repositories
 * @version February 10, 2022, 5:10 am UTC
*/

class UnitRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'unit_code'
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
        return Unit::class;
    }
}
