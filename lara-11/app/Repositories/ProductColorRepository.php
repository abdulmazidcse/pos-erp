<?php

namespace App\Repositories;

use App\Models\ProductColor;
use App\Repositories\BaseRepository;

/**
 * Class ProductColorRepository
 * @package App\Repositories
 * @version March 6, 2022, 12:52 pm UTC
*/

class ProductColorRepository extends BaseRepository
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
        return ProductColor::class;
    }
}
