<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Repositories\BaseRepository;

/**
 * Class WarehouseRepository
 * @package App\Repositories
 * @version February 23, 2022, 6:17 am UTC
*/

class WarehouseRepository extends BaseRepository
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
        return Warehouse::class;
    }
}
