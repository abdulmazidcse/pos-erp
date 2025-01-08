<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasPermissionsTrait {

    // give permission
    public function givePermissionTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if($permissions == null)
        {
            return $this;
        }

        $this->permissions()->saveMany($permissions);
        return $this;
    }

    public function withdrawPermissionsTo( ...$permissions ) {

        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;

    }

    public function syncPermissions( ...$permissions ) {

        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }

    // Get Permissions
    public function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }

    // Check Has Permission
    public function hasPermission($permission)
    {
        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }

    //check has role
    public function hasRole(...$roles)
    {
        foreach ($roles as $role)
        {
            if($this->roles->contains('slug', $role->slug)){
                return true;
            }
        }
        return false;
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    // check permission through role
    public function hasPermissionThroughRole($permissions)
    {
        foreach ($permissions->roles as $role) {
            if($this->roles->contains($role)){
                return true;
            }
        }
        return false;
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }
}