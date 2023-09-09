<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        $user = User::insert([
            ['name'=>'Super Admin', 'email' => 'superadmin@gmail.com', 'phone'=>'01700100100', 'password' => bcrypt('password')],
            ['name'=>'Admin', 'email' => 'admin@gmail.com', 'phone'=>'01700100101', 'password' => bcrypt('password')]
        ]);
    }
}
