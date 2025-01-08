<?php

namespace App\Repositories;

use App\Models\ProductBarcodes;
use App\Repositories\BaseRepository;

/**
 * Class ProductBarcodesRepository
 * @package App\Repositories
 * @version March 2, 2022, 1:15 pm UTC
*/

class ProductBarcodesRepository extends BaseRepository
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
        return ProductBarcodes::class;
    }
}
