<?php

namespace App\Repositories;

use App\Models\DiscountSetting;
use App\Repositories\BaseRepository;

/**
 * Class DiscountSettingRepository
 * @package App\Repositories
 * @version May 18, 2022, 10:01 am UTC
*/

class DiscountSettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_wise',
        'customer_group_wise',
        'individual_customer',
        'product_wise',
        'category_wise',
        'sub_category_wise',
        'vendor_wise',
        'slow_moving_product',
        'slow_moving_product_discount',
        'fast_moving_product',
        'fast_moving_product_discount',
        'sales_platform',
        'sales_platform_pos',
        'sales_platform_pos_discount',
        'sales_platform_ecom',
        'sales_platform_ecom_discount',
        'gp_wise',
        'gp_wise_discount',
        'enable_conditional_discount',
        'discount_within_range'
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
        return DiscountSetting::class;
    }
}
