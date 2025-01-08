<?php

namespace App\Repositories;

use App\Models\Outlet;
use App\Repositories\BaseRepository;

/**
 * Class OutletRepository
 * @package App\Repositories
 * @version February 7, 2022, 8:52 am UTC
*/

class OutletRepository extends BaseRepository
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
        return Outlet::class;
    }
}
