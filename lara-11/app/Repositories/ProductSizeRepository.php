<?php

namespace App\Repositories;

use App\Models\ProductSize;
use App\Repositories\BaseRepository;

/**
 * Class ProductSizeRepository
 * @package App\Repositories
 * @version March 6, 2022, 12:53 pm UTC
*/

class ProductSizeRepository extends BaseRepository
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
        return ProductSize::class;
    }
}
