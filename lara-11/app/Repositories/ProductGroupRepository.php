<?php

namespace App\Repositories;

use App\Models\ProductGroup;
use App\Repositories\BaseRepository;

/**
 * Class ProductGroupRepository
 * @package App\Repositories
 * @version April 13, 2022, 9:31 am UTC
*/

class ProductGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'product_id',
        'group_id',
        'attribute_id',
        'attribute_value_id'
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
        return ProductGroup::class;
    }
}
