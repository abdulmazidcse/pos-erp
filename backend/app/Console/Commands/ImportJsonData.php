<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PermissionModule; 
use App\Models\Permission; 
use App\Models\Role;
use App\Models\User; 
use Illuminate\Support\Facades\Schema;
use DB;
class ImportJsonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:json-data';

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
        $this->dbtruncate();
        // Import permission module
        self::permissionModuleImport();
        // Import permission
        self::permissionImport();
       
        self::userRole();
        self::user();
        
        self::roleHasPermissions();

        self::modelHasRole();

        $this->info('Data imported successfully.');
    }

    protected function dbtruncate(){
        Schema::disableForeignKeyConstraints();
        PermissionModule::truncate();
        Permission::truncate();
        Role::truncate();
        User::truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        Schema::enableForeignKeyConstraints();
    }

    protected function permissionModuleImport(){
        $permissionModuleJsonFile = public_path('needle/permission_modules.json');
        $permissionModulejsonData = json_decode(file_get_contents($permissionModuleJsonFile), true);      

        // Iterate over the JSON data and insert it into the database
        foreach ($permissionModulejsonData[0]['data'] as $item) {
            PermissionModule::create([
                'id' => $item['id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'icon_name' => $item['icon_name'],
                'parent_id' => $item['parent_id'],
                'is_action_menu' => $item['is_action_menu'],
                'is_multiple_action' => $item['is_multiple_action'],
                'is_children' => $item['is_children'],
                'menu_order' => $item['menu_order'],
                'total_actions' => $item['total_actions'],
                'columnable_permission' => $item['columnable_permission'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s') 
            ]);
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
}
