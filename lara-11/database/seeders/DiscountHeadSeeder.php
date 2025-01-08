<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountTitle;
class DiscountHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DiscountTitle::truncate();

        $user = DiscountTitle::insert([
            ['title'=>'Customer wise discount ', 'order_by' => 1  ],
            ['title'=>'Product wise discount', 'order_by' => 2  ],
            ['title'=>'Category wise discount', 'order_by' => 3  ],
            ['title'=>'Sub - Category wise discount', 'order_by' => 4  ],
            ['title'=>'Vendor wise discount', 'order_by' => 5  ],
            ['title'=>'Slow moving product discount', 'order_by' => 6  ],
            ['title'=>'Fast moving product discount', 'order_by' => 7  ],
            ['title'=>'Conditional discount ', 'order_by' => 8  ],
            ['title'=>'Sales platform wise discount ', 'order_by' => 9  ],
            ['title'=>'GP wise discount', 'order_by' => 10  ]
        ]);
    }
}
