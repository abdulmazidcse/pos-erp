<?php

namespace App\Repositories;

use App\Models\Size;
use App\Repositories\BaseRepository;

/**
 * Class SizeRepository
 * @package App\Repositories
 * @version March 7, 2022, 5:26 am UTC
*/

class SizeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'code'
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
        return Size::class;
    }
}
