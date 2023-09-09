<?php

namespace App\Repositories;

use App\Models\ProductImages;
use App\Repositories\BaseRepository;

/**
 * Class ProductImagesRepository
 * @package App\Repositories
 * @version March 2, 2022, 1:13 pm UTC
*/

class ProductImagesRepository extends BaseRepository
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
        return ProductImages::class;
    }
}
