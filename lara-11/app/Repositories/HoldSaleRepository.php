<?php

namespace App\Repositories;

use App\Models\HoldSale;
use App\Repositories\BaseRepository;

/**
 * Class HoldSaleRepository
 * @package App\Repositories
 * @version June 7, 2022, 5:38 pm UTC
*/

class HoldSaleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ss_id'
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
        return HoldSale::class;
    }
}
