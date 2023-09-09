<?php

namespace App\Repositories;

use App\Models\PaymentCollection;
use App\Repositories\BaseRepository;

/**
 * Class PaymentCollectionRepository
 * @package App\Repositories
 * @version April 21, 2022, 8:16 am UTC
*/

class PaymentCollectionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sale_id',
        'paying_by',
        'amount',
        'payment_note'
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
        return PaymentCollection::class;
    }
}
