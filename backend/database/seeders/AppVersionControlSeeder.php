<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppVersionControlSeeder extends Seeder
{
    public function run()
    {
        DB::table('app_version_control')->insert([
            [
                'platform' => 'Android',
                'latest_version' => '1.2.0',
                'minimum_version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'iOS',
                'latest_version' => '1.2.0',
                'minimum_version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
