<?php

namespace App\Repositories;

use App\Models\DiscountTitle;
use App\Repositories\BaseRepository;

/**
 * Class DiscountTitleRepository
 * @package App\Repositories
 * @version May 16, 2022, 7:14 am UTC
*/

class DiscountTitleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'order_by'
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
        return DiscountTitle::class;
    }
}
