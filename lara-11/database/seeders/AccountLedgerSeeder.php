<?php

namespace Database\Seeders;

use App\Models\AccountLedger;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AccountLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        AccountLedger::truncate();

        Schema::enableForeignKeyConstraints();


        AccountLedger::create(['ledger_code' => 1, 'ledger_name' => 'Assets', 'is_master_head' => 1 ]);
        AccountLedger::create(['ledger_code' => 2, 'ledger_name' => 'Liabilities', 'is_master_head' => 1 ]);
        AccountLedger::create(['ledger_code' => 3, 'ledger_name' => 'Equities', 'is_master_head' => 1 ]);
        AccountLedger::create(['ledger_code' => 4, 'ledger_name' => 'Income', 'is_master_head' => 1 ]);
        AccountLedger::create(['ledger_code' => 5, 'ledger_name' => 'Expenditure', 'is_master_head' => 1 ]);
    }
}
