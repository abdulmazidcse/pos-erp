<?php

namespace App\Repositories;

use App\Models\PurchaseReceive;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseReceiveRepository
 * @package App\Repositories
 * @version April 3, 2022, 8:57 am UTC
*/

class PurchaseReceiveRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'reference_no'
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
        return PurchaseReceive::class;
    }
}
