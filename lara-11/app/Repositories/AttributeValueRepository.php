<?php

namespace App\Repositories;

use App\Models\AttributeValue;
use App\Repositories\BaseRepository;

/**
 * Class AttributeValueRepository
 * @package App\Repositories
 * @version April 13, 2022, 9:28 am UTC
*/

class AttributeValueRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'attrbute_id',
        'value'
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
        return AttributeValue::class;
    }
}
