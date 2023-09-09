<?php

namespace App\Repositories;

use App\Models\ProductCategory;
use App\Repositories\BaseRepository;

/**
 * Class ProductCategoryRepository
 * @package App\Repositories
 * @version January 6, 2022, 6:26 am UTC
*/

class ProductCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name','parent_id','id'
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
        return ProductCategory::class;
    }
}
