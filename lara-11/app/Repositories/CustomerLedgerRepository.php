<?php

namespace App\Repositories;

use App\Models\CustomerLedger;
use App\Repositories\BaseRepository;

/**
 * Class CustomerLedgerRepository
 * @package App\Repositories
 * @version May 13, 2022, 7:43 pm UTC
*/

class CustomerLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'transaction_type',
        'opening_balance',
        'collection_amount',
        'return_amount',
        'discount_amount',
        'closing_balance',
        'transaction_date'
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
        return CustomerLedger::class;
    }
}
