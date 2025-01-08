<?php

namespace App\Repositories;

use App\Models\MobileWallet;
use App\Repositories\BaseRepository;

/**
 * Class MobileWalletRepository
 * @package App\Repositories
 * @version April 11, 2022, 7:50 am UTC
*/

class MobileWalletRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'mobile_wallet',
        'agent_name',
        'mobile_number'
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
        return MobileWallet::class;
    }
}
