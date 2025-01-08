<?php

namespace App\Repositories;

use App\Models\PointsSettings;
use App\Repositories\BaseRepository;

/**
 * Class PointsSettingsRepository
 * @package App\Repositories
 * @version May 8, 2022, 7:01 am UTC
*/

class PointsSettingsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'enable_points_rewards',
        'enable_signup_points',
        'signup_points',
        'enable_referral_points',
        'referral_points',
        'enable_social_point',
        'social_share_facebook',
        'social_share_twitter',
        'custom_points_on_cart',
        'cart_points_rate',
        'cart_price_rate',
        'enable_points_order_total'
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
        return PointsSettings::class;
    }
}
