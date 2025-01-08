<?php

namespace App\Repositories;

use App\Models\FiscalYear;
use App\Repositories\BaseRepository;

/**
 * Class FiscalYearRepository
 * @package App\Repositories
 * @version July 30, 2022, 6:56 am UTC
*/

class FiscalYearRepository extends BaseRepository
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
        return FiscalYear::class;
    }
}
