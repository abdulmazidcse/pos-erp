<?php

namespace App\Repositories;

use App\Models\ProductSupplier;
use App\Repositories\BaseRepository;

/**
 * Class ProductSupplierRepository
 * @package App\Repositories
 * @version March 6, 2022, 6:06 am UTC
*/

class ProductSupplierRepository extends BaseRepository
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
        return ProductSupplier::class;
    }
}
