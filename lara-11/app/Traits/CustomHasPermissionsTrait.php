<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\PermissionModule;
use App\Models\Role;

trait CustomHasPermissionsTrait {

    // Get Permissions
    public function getAllPermissionsWithMenu()
    {
        $parent_modules = PermissionModule::where('parent_id', 0)->orderBy('menu_order', 'ASC')->get();

        $final_array = [];
        foreach ($parent_modules as $parent_module) {
            $final_array[$parent_module->slug] = [
                'id'    => $parent_module->id,
            ];
        }
    }


    public function getAllActionsByMenu()
    {

    }

}