<?php

namespace App\Repositories;

use App\Models\BankAccount;
use App\Repositories\BaseRepository;

/**
 * Class AccountRepository
 * @package App\Repositories
 * @version May 16, 2022, 7:07 am UTC
*/

class BankAccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'account_no'
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
        return BankAccount::class;
    }
}
