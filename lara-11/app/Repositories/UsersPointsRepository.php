<?php

namespace App\Repositories;

use App\Models\UsersPoints;
use App\Repositories\BaseRepository;

/**
 * Class UsersPointsRepository
 * @package App\Repositories
 * @version May 9, 2022, 7:36 am UTC
*/

class UsersPointsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'sale_id',
        'type',
        'points'
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
        return UsersPoints::class;
    }
}
