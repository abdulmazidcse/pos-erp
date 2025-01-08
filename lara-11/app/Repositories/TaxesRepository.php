<?php

namespace App\Repositories;

use App\Models\Taxes;
use App\Repositories\BaseRepository;

/**
 * Class TaxesRepository
 * @package App\Repositories
 * @version April 4, 2022, 9:59 am UTC
*/

class TaxesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title'
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
        return Taxes::class;
    }
}
