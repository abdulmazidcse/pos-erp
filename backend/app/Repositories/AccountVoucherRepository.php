<?php

namespace App\Repositories;

use App\Models\AccountVoucher;
use App\Repositories\BaseRepository;

/**
 * Class AccountVoucherRepository
 * @package App\Repositories
 * @version August 4, 2022, 11:13 am UTC
*/

class AccountVoucherRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code'
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
        return AccountVoucher::class;
    }
}
