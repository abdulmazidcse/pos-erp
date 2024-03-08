<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use App\Models\PermissionModule; 
use App\Models\Permission; 
use App\Models\Role;
use App\Models\User; 
use App\Models\Division; 
use App\Models\Unit; 
use App\Models\AccountClass; 
use App\Models\AccountType; 
use App\Models\AccountLedger; 
use App\Models\EntryType; 
use App\Models\GeneralSetting; 
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use DB; 
class ImportJsonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'import:json-data';
    protected $signature = 'import:json-data {input-field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from JSON file into the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        Schema::disableForeignKeyConstraints(); 
        $this->info('DB truncate Started. Please wait moment.');
        $this->dbtruncate();
        $this->info('DB truncate successfully.');
        $purchasesKey = 'er23-2023-erps';
        $inputField = $this->argument('input-field');
        
        if($purchasesKey == $inputField){ 
            $this->info('Started importing. Please wait moment.');
            // Import Role module
            self::userRole();
            // Import User module
            self::user();
            // Import permission module
            self::permissionModuleImport();
            // Import permission
            self::permissionImport();
            // Import 
            self::roleHasPermissions();
            // Import 
            self::modelHasRole();
            // Import units Data
            self::units();
            // Import Division Data
            self::divisions();
            // Import accountClasss Data
            self::accountClasses();
            // Import AccountType Data
            self::accountTypes();
            // Import AccountLedger Data
            self::accountLedgers();
            // Import Entry Type Data
            self::entryType();
            // Import General Setting Data
            self::generalSettings($purchasesKey); 
            $this->info('Data imported successfully.');
        }else{
            $this->info('Your given key is invalid, please give valid key'); 
        } 
        Schema::enableForeignKeyConstraints();
        self::deleteFiles(); 
    }

    protected function dbtruncate(){ 
        PermissionModule::truncate();
        Permission::truncate();
        Role::truncate();
        User::truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        Division::truncate();
        Unit::truncate();
        AccountClass::truncate();
        AccountType::truncate();
        AccountLedger::truncate();
        EntryType::truncate(); 
        GeneralSetting::truncate(); 
    }

    protected function permissionModuleImport(){
        $permissionModuleJsonFile = public_path('needle/permission_modules.json');
        $permissionModulejsonData = json_decode(file_get_contents($permissionModuleJsonFile), true);      

        // Iterate over the JSON data and insert it into the database
        foreach ($permissionModulejsonData[0]['data'] as $item) {
            DB::insert('insert into permission_modules (id, name, slug, icon_name, parent_id, is_action_menu, 
                        is_multiple_action, is_children, menu_order, total_actions, columnable_permission, created_at, updated_at) 
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$item['id'], $item['name'], $item['slug'], $item['icon_name'], $item['parent_id'], $item['is_action_menu'], $item['is_multiple_action'], 
                $item['is_children'], $item['menu_order'], $item['total_actions'], $item['columnable_permission'], date('Y-m-d h:i:s'), date('Y-m-d h:i:s')]); 
        } 
    }
    protected function permissionImport(){
        $permissionJsonFilejsonFile = public_path('needle/permissions.json');
        $permissionjsonData = json_decode(file_get_contents($permissionJsonFilejsonFile), true);
        foreach ($permissionjsonData[0]['data'] as $item) {
            Permission::create([
                'id' => $item['id'],
                'module_id' => $item['module_id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'url_path' => $item['url_path'],
                'component_path' => $item['component_path'], 
                'column_status' => $item['column_status'],
                'is_route_action' => $item['is_route_action'],
                'is_nav' => $item['is_nav'],
                'is_index' => $item['is_index'],
                'guard_name' => $item['guard_name'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') 
            ]);
        }
    }
    protected function userRole(){
        $roleJsonFilejsonFile = public_path('needle/roles.json');
        $rolejsonData = json_decode(file_get_contents($roleJsonFilejsonFile), true);
        foreach ($rolejsonData[0]['data'] as $item) {
            Role::create([
                'id' => $item['id'], 
                'name' => $item['name'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'guard_name' => $item['guard_name'],   
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') 
            ]);
        }
    }
    protected function modelHasRole(){
        $modelHasRoleJsonFilejsonFile = public_path('needle/model-has-role.json');
        $modelHasRolejsonData = json_decode(file_get_contents($modelHasRoleJsonFilejsonFile), true);
        foreach ($modelHasRolejsonData[0]['data'] as $item) {
            DB::insert('insert into model_has_roles (role_id, model_type, model_id) values (?, ?, ?)',
            [$item['role_id'], $item['model_type'], $item['model_id']]); 
        } 
    }
    protected function user(){ 
        $usersJsonFilejsonFile = public_path('needle/users.json');
        $usersjsonData = json_decode(file_get_contents($usersJsonFilejsonFile), true);
        foreach ($usersjsonData[0]['data'] as $item) {
            User::create([
                'id' => $item['id'],
                'user_code' => $item['user_code'],
                'name' => $item['name'],
                'email' => $item['email'],
                'phone' => $item['phone'],
                'email_verified_at' => $item['email_verified_at'], 
                'password' => $item['password'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') 
            ]);
        }
    }
    protected function roleHasPermissions(){ 
        $roleHasJsonFilejsonFile = public_path('needle/role_has_permissions.json');
        $roleHasjsonData = json_decode(file_get_contents($roleHasJsonFilejsonFile), true);
        foreach ($roleHasjsonData[0]['data'] as $item) {
            DB::insert('insert into role_has_permissions (permission_id, role_id) values (?, ?)',
            [$item['permission_id'], $item['role_id']]); 
        }
    }
    protected function units(){
        $itsJsonFile = public_path('needle/units.json'); 
        $usersJsonData = json_decode(file_get_contents($itsJsonFile), true);
        foreach ($usersJsonData[0]['data'] as $item) {   
            Unit::create([
                'id' => $item['id'],
                'unit_code' => $item['unit_code'],
                'unit_name' => $item['unit_name'],
                'base_unit' => $item['base_unit'],
                'operator' => $item['operator'],
                'operation_value' => $item['operation_value'], 
                'status' => $item['status'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }
    protected function divisions(){
        $divisionsJsonFile = public_path('needle/divisions.json'); 
        $divisionsJsonData = json_decode(file_get_contents($divisionsJsonFile), true);  
        foreach ($divisionsJsonData[0]['data'] as $item) {   
            Division::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'bn_name' => $item['bn_name'],  
                'status'  => $item['status'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }
    protected function accountClasses(){
        $accountClassesJsonFile = public_path('needle/account_classes.json'); 
        $accountClassesJsonData = json_decode(file_get_contents($accountClassesJsonFile), true);   
        foreach ($accountClassesJsonData[0]['data'] as $item) {   
            AccountClass::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'code' => $item['code'],  
                'is_debit_positive'  => $item['is_debit_positive'], 
                'is_credit_positive'  => $item['is_credit_positive'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }
    protected function accountTypes(){
        $accountTypesJsonFile = public_path('needle/account_types.json'); 
        $accountTypesJsonData = json_decode(file_get_contents($accountTypesJsonFile), true);   
        foreach ($accountTypesJsonData[0]['data'] as $item) {     
            AccountType::create([
                'id' => $item['id'],
                'type_code' => $item['type_code'],  
                'type_name' => $item['type_name'],
                'class_id'  => $item['class_id'], 
                'parent_type_id'  => $item['parent_type_id'], 
                'status'  => $item['status'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }
    protected function accountLedgers(){
        $accountLedgersJsonFile = public_path('needle/account_ledgers.json'); 
        $accountLedgersJsonData = json_decode(file_get_contents($accountLedgersJsonFile), true);   

        foreach ($accountLedgersJsonData[0]['data'] as $item) {     
            AccountLedger::create([
                'id' => $item['id'],
                'ledger_code' => $item['ledger_code'],  
                'ledger_name' => $item['ledger_name'],
                'parent_id'  => $item['parent_id'], 
                'type_id'  => $item['type_id'], 
                'detail_type_id'  => $item['detail_type_id'], 
                'ledger_type'  => $item['ledger_type'], 
                'opening_balance'  => $item['opening_balance'], 
                'balance_date'  => $item['balance_date'], 
                'is_control_transaction'  => $item['is_control_transaction'], 
                'is_master_head'  => $item['is_master_head'], 
                'status'  => $item['status'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }
    protected function entryType(){
        $entryTypesJsonFile = public_path('needle/entry_types.json'); 
        $entryTypesJsonData = json_decode(file_get_contents($entryTypesJsonFile), true);   
        foreach ($entryTypesJsonData[0]['data'] as $item) {    
            EntryType::create([
                'id' => $item['id'],
                'label' => $item['label'],  
                'name' => $item['name'],
                'description'  => $item['description'], 
                'numbering'  => $item['numbering'], 
                'prefix'  => $item['prefix'], 
                'suffix'  => $item['suffix'], 
                'zero_padding'  => $item['zero_padding'], 
                'restrictions'  => $item['restrictions'], 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }
    protected function generalSettings($purchase_key){
        $generalSetingJsonFile = public_path('needle/general_settings.json'); 
        $generalSettingJsonData = json_decode(file_get_contents($generalSetingJsonFile), true);   
        $hostname = gethostname();
        $ipAddress = gethostbyname($hostname);
        foreach ($generalSettingJsonData[0]['data'] as $item) {    
            GeneralSetting::create([
                'id' => $item['id'],
                'invoice_sms_status' => $item['invoice_sms_status'],  
                'payment_status' => $item['payment_status'],
                'date_status'  => $item['date_status'], 
                'date_format'  => $item['date_format'], 
                'api_key'  => $item['api_key'], 
                'sender_id'  => $item['sender_id'], 
                'sms_text'  => $item['sms_text'], 
                'purchase_key'  => $purchase_key, 
                'ip_address'  => $ipAddress, 
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') ,
                "deleted_at" => null
            ]);
        } 
    }

    protected function downloadFiles(){
 
        $folder = 'needle';
        $url = 'http://localhost/pos-dev/' . $folder; // Replace with your actual URL
        $sourceDirectory = 'E:/laragon/www/pos-dev/' . $folder;
        $destinationPath = public_path('downloads');

        // Ensure the destination directory exists in the public folder
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        // Fetch the list of files in the source directory
        $files = File::files($sourceDirectory);

        if (!empty($files)) {
            // Download and save each file to the destination directory
            foreach ($files as $file) {
                $fileContents = File::get($file);
                $fileName = pathinfo($file, PATHINFO_BASENAME);
                File::put($destinationPath . '/' . $fileName, $fileContents);
            }

            $this->info("Downloaded files for folder: $folder");
        } else {
            $this->error("No files found in the source directory: $sourceDirectory");
        }
        
        
    } 
    protected function deleteFiles(){
 
        $folder = public_path('downloads'); // Path to the download folder
        // Check if the folder exists
        if (File::exists($folder)) {
            // Get a list of files in the folder
            $files = File::files($folder);

            // Loop through the files and delete them
            foreach ($files as $file) {
                File::delete($file);
            }

            $this->info('Deleted all files in the download folder');
        } else {
            $this->info('Download folder does not exist or is empty.');
        }        
        
    } 

    public function dropAllTables()
    {
        // Get the list of all tables in the database
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = reset($table); // Extract the table name from the stdClass object

            // Drop each table
            DB::statement('DROP TABLE IF EXISTS ' . $tableName);
        }

        echo "All tables have been dropped.";
    }

}