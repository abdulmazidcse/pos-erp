<?php

namespace App\Repositories;

use App\Models\HoldSaleItem;
use App\Repositories\BaseRepository;

/**
 * Class HoldSaleItemRepository
 * @package App\Repositories
 * @version June 7, 2022, 5:39 pm UTC
*/

class HoldSaleItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sale_id'
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
        return HoldSaleItem::class;
    }
}
