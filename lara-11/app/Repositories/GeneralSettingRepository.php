<?php

namespace App\Repositories;

use App\Models\GeneralSetting;
use App\Repositories\BaseRepository;

/**
 * Class GeneralSettingRepository
 * @package App\Repositories
 * @version October 26, 2022, 3:16 am UTC
*/

class GeneralSettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'payment_status',
        'date_status',
        'date_format',
        'sender_id'
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
        return GeneralSetting::class;
    }
}
