<?php

namespace App\Repositories;

use App\Models\ProductKeywords;
use App\Repositories\BaseRepository;

/**
 * Class ProductKeywordsRepository
 * @package App\Repositories
 * @version March 2, 2022, 1:14 pm UTC
*/

class ProductKeywordsRepository extends BaseRepository
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
        return ProductKeywords::class;
    }
}
