<?php

namespace App\Repositories;

use App\Models\SaleItem;
use App\Repositories\BaseRepository;

/**
 * Class SaleItemRepository
 * @package App\Repositories
 * @version April 21, 2022, 8:05 am UTC
*/

class SaleItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sale_id',
        'product_id',
        'quantity',
        'discount',
        'item_quantity',
        'vat',
        'vat_id',
        'inite_price',
        'net_unit_price',
        'cost_price'
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
        return SaleItem::class;
    }
}
