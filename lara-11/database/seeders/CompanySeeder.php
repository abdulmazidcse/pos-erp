<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::truncate(); 
        $raw_query = "INSERT INTO `companies` (`id`, `name`, `logo`, `address`, `contact_person_name`, `contact_person_number`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 'SSG', 'company-logo.png', 'UCEP Cheyne Tower (2nd floor) 25, Segun Bagicha, Dhaka 1000', 'Contact person name', '018xxxxxxxx', 1, '2022-02-17 04:38:13', '2022-02-17 04:38:13', NULL)"; 
        \DB::select(\DB::raw($raw_query));
    }
}
