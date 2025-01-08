<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionModule;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
            TRUNCATE permission_modules;
            TRUNCATE permissions;
            TRUNCATE role_has_permissions;
            TRUNCATE model_has_roles;
            TRUNCATE model_has_permissions;
            TRUNCATE roles;
        */

        Schema::disableForeignKeyConstraints();


//        \DB::table('permission_modules')->truncate();
        \DB::table('permissions')->truncate();
        \DB::table('role_has_permissions')->truncate();
        //\DB::table('model_has_roles')->truncate();
        \DB::table('model_has_permissions')->truncate();
        //\DB::table('roles')->truncate();

        Schema::enableForeignKeyConstraints();

//
//        // Create Role
////        $roleSuperAdmin = Role::find(1);
//        $roleSuperAdmin = Role::create([
//            'name'          => 'Super Admin',
//            'slug'          => 'super-admin',
//            'description'   => 'Access all permission...'
//        ]);
//        // $roleUser       = Role::create(['name' => 'user', 'guard_name' => 'admin']);
//
//        // Create Permission Module
//        $permission_module = PermissionModule::create(['name'=>'Dashboard', 'slug'=>'dashboard']);
//        $permission_module1 = PermissionModule::create(['name'=>'Role', 'slug'=>'role']);
//        $permission_module2 = PermissionModule::create(['name'=>'User', 'slug'=>'user']);
//        $permission_module3 = PermissionModule::create(['name'=>'Permission Module', 'slug'=>'permission-module']);
//        $permission_module4 = PermissionModule::create(['name'=>'Permission', 'slug'=>'permission']);
//
//
//        // Permission List In Array
//        $permissions = [
//            // Dashboard
//            [
//                'module_id'  => $permission_module->id,
//                'permissions' => [
//                    [
//                        'module_id' => $permission_module->id,
//                        'name'  => 'Dashboard View',
//                        'slug'  => 'dashboard-view',
//                    ]
//                ],
//            ],
//
//            // Role
//            [
//                'module_id'  => $permission_module1->id,
//                'permissions' => [
//                    [
//                        'module_id' => $permission_module1->id,
//                        'name'  => 'Role Index',
//                        'slug'  => 'role-index',
//                    ],
//                    [
//                        'module_id' => $permission_module1->id,
//                        'name'  => 'Role View',
//                        'slug'  => 'role-view',
//                    ],
//                    [
//                        'module_id' => $permission_module1->id,
//                        'name'  => 'Role Create',
//                        'slug'  => 'role-create',
//                    ],
//                    [
//                        'module_id' => $permission_module1->id,
//                        'name'  => 'Role Edit',
//                        'slug'  => 'role-edit',
//                    ],
//                    [
//                        'module_id' => $permission_module1->id,
//                        'name'  => 'Role Delete',
//                        'slug'  => 'role-delete',
//                    ],
//                    [
//                        'module_id' => $permission_module1->id,
//                        'name'  => 'Role Permission',
//                        'slug'  => 'role-permission',
//                    ],
//                ]
//            ],
//
//            // User
//            [
//                'module_id'  => $permission_module2->id,
//                'permissions' => [
//                    [
//                        'module_id' => $permission_module2->id,
//                        'name'  => 'User Index',
//                        'slug'  => 'user-index',
//                    ],
//                    [
//                        'module_id' => $permission_module2->id,
//                        'name'  => 'User View',
//                        'slug'  => 'user-view',
//                    ],
//                    [
//                        'module_id' => $permission_module2->id,
//                        'name'  => 'User Create',
//                        'slug'  => 'user-create',
//                    ],
//                    [
//                        'module_id' => $permission_module2->id,
//                        'name'  => 'User Edit',
//                        'slug'  => 'user-edit',
//                    ],
//                    [
//                        'module_id' => $permission_module2->id,
//                        'name'  => 'User Delete',
//                        'slug'  => 'user-delete',
//                    ],
//                    [
//                        'module_id' => $permission_module2->id,
//                        'name'  => 'User Permission',
//                        'slug'  => 'user-permission',
//                    ],
//                ]
//            ],
//
//            // Permission Module
//            [
//                'module_id'  => $permission_module3->id,
//                'permissions' => [
//                    [
//                        'module_id' => $permission_module3->id,
//                        'name'  => 'Permission Module Index',
//                        'slug'  => 'permission-module-index',
//                    ],
//                    [
//                        'module_id' => $permission_module3->id,
//                        'name'  => 'Permission Module View',
//                        'slug'  => 'permission-module-view',
//                    ],
//                    [
//                        'module_id' => $permission_module3->id,
//                        'name'  => 'Permission Module Create',
//                        'slug'  => 'permission-module-create',
//                    ],
//                    [
//                        'module_id' => $permission_module3->id,
//                        'name'  => 'Permission Module Edit',
//                        'slug'  => 'permission-module-edit',
//                    ],
//                    [
//                        'module_id' => $permission_module3->id,
//                        'name'  => 'Permission Module Delete',
//                        'slug'  => 'permission-module-delete',
//                    ],
//                ]
//            ],
//
//            // Permission
//            [
//                'module_id'  => $permission_module4->id,
//                'permissions' => [
//                    [
//                        'module_id' => $permission_module4->id,
//                        'name'  => 'Permission Index',
//                        'slug'  => 'permission-index',
//                    ],
//                    [
//                        'module_id' => $permission_module4->id,
//                        'name'  => 'Permission View',
//                        'slug'  => 'permission-view',
//                    ],
//                    [
//                        'module_id' => $permission_module4->id,
//                        'name'  => 'Permission Create',
//                        'slug'  => 'permission-create',
//                    ],
//                    [
//                        'module_id' => $permission_module4->id,
//                        'name'  => 'Permission Edit',
//                        'slug'  => 'permission-edit',
//                    ],
//                    [
//                        'module_id' => $permission_module4->id,
//                        'name'  => 'Permission Delete',
//                        'slug'  => 'permission-delete',
//                    ],
//                ]
//            ],
//
//
//
//        ];
//
//        // Assign Permission
//        for ($i = 0; $i < count($permissions); $i++) {
//
//            for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
//                $permission = Permission::create($permissions[$i]['permissions'][$j]);
//                $roleSuperAdmin->givePermissionTo($permission);
//                $permission->assignRole($roleSuperAdmin);
//            }
//        }
//        $user = User::find(1);
//        $user->assignRole($roleSuperAdmin);


    }
}
