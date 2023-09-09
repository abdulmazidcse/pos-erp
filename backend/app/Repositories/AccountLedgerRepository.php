<?php

namespace App\Repositories;

use App\Models\AccountLedger;
use App\Repositories\BaseRepository;

/**
 * Class AccountLedgerRepository
 * @package App\Repositories
 * @version July 21, 2022, 6:34 am UTC
*/

class AccountLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ledger_code'
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
        return AccountLedger::class;
    }
}
