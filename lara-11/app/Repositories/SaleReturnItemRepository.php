<?php

namespace App\Repositories;

use App\Models\SaleReturnItem;
use App\Repositories\BaseRepository;

/**
 * Class SaleReturnItemRepository
 * @package App\Repositories
 * @version July 16, 2022, 11:43 am UTC
*/

class SaleReturnItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sale_id',
        'sale_item_id',
        'return_type'
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
        return SaleReturnItem::class;
    }
}
