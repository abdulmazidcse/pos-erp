<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRoleAPIRequest;
use App\Http\Requests\API\UpdateRoleAPIRequest;
use App\Http\Resources\RoleResource;
use App\Models\PermissionModule;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Response;
use App\Models\Permission;
use App\Models\Role;

/**
 * Class RoleController
 * @package App\Http\Controllers\API
 */

class RoleAPIController extends AppBaseController
{
    /** @var  RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Role.
     * GET|HEAD /roles
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $roles = $this->roleRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $return_data    = RoleResource::collection($roles);

        return $this->sendResponse($return_data, 'Roles retrieved successfully');
    }


    public function create()
    {

        $permission_modules  = PermissionModule::all();
//        dd(empty($permission_modules));
        if(empty($permission_modules)){
            return $this->sendError('Permissions data not found');
        }

        $permission_array = [];
        foreach ($permission_modules as $module){
            $permission_array[$module->id] = Permission::getPermissionByModuleId($module->id);
        }

        $all_permissions    = Permission::all()->map(function ($permission){

            return $permission->id;
        });

        $data   = [
            'permission_modules' => $permission_modules,
            'permissions'       => $permission_array,
            'all_permissions'   => $all_permissions
        ];

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    /**
     * Store a newly created Role in storage.
     * POST /roles
     *
     * @param CreateRoleAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateRoleAPIRequest $request)
    {
        $this->validate($request, [
            'name'                => 'required|unique:roles,name',
        ], [
            'name.unique' => 'This Role Name Already Exists',
        ]);

        //dd($request->rules());
        $input = $request->except(['permissions']);
        $input['slug'] = strtolower(str_replace(' ','-', $request->name));

        $permissions = json_decode($request->input('permissions'));

        DB::beginTransaction();
        try{
            $role = $this->roleRepository->create($input);

            if($role && !empty($permissions)){
                $permission_setup = $role->syncPermissions($permissions);
            }

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message'   => 'Role Save Failed',
                'error'     => $e->getMessage()
            ]);
        }


        return $this->sendResponse($role->toArray(), 'Role saved successfully');
    }

    /**
     * Display the specified Role.
     * GET|HEAD /roles/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $role_module_permissions = [];
        if(count($role->permissions) > 0) {
            foreach($role->permissions as $permission) {
                $role_module_permissions[$permission->module_id][] = [
                    'id'    => $permission->id,
                    'module_id' => $permission->module_id,
                    'module_name'   => $permission->permission_modules->name ?? 'Default',
                    'module_slug'   => $permission->permission_modules->slug ?? 'N/A',
                    'name'  => $permission->name,
                    'slug'  => $permission->slug,
                    'column_status' => $permission->column_status
                ];
            }
        }

        $return_data    = [
            'role'  => new RoleResource($role),
            'role_module_permissions'   => $role_module_permissions
        ];

        return $this->sendResponse($return_data, 'Role retrieved successfully');
    }

    //For Edit
    public function edit($id)
    {
        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $permissions = Permission::all();
        $permission_groups  = [];

        $data   = [
            'permission_groups' => $permission_groups,
            'permissions'       => $permissions,
            'role'              => new RoleResource($role)
        ];

        //$return_data    = new RoleResource($role);

        return $this->sendResponse($data, 'Role and permission data retrieved successfully');
    }

    /**
     * Update the specified Role in storage.
     * PUT/PATCH /roles/{id}
     *
     * @param int $id
     * @param UpdateRoleAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRoleAPIRequest $request)
    {
        $this->validate($request, [
            'name'                => 'required|unique:roles,name,'.$id,
        ], [
            'name.unique' => 'This Role Name Already Exists',
        ]);

        $input = $request->except('permissions');
        $permissions    = json_decode($request->input('permissions'));

        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        DB::beginTransaction();
        try{
            $role = $this->roleRepository->update($input, $id);
            if($role && !empty($permissions)){
                $role->syncPermissions($permissions);
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message'   => 'Role Update Failed',
                'error'     => $e->getMessage()
            ]);
        }


        return $this->sendResponse($role->toArray(), 'Role updated successfully');
    }

    /**
     * Remove the specified Role from storage.
     * DELETE /roles/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $role->delete();

        return $this->sendSuccess('Role deleted successfully');
    }

    /** Role Permission get */
//    public function rolePermissionGet($id)
//    {
//        /** @var Role $role */
//        $role = $this->roleRepository->find($id);
//
//        if (empty($role)) {
//            return $this->sendError('Role not found');
//        }
//
//        $role_module_permissions = [];
//        if(count($role->permissions) > 0) {
//            foreach($role->permissions as $permission) {
//                $role_module_permissions[$permission->module_id][] = [
//                    'id'    => $permission->id,
//                    'module_id' => $permission->module_id,
//                    'module_name'   => $permission->permission_modules->name ?? 'Default',
//                    'module_slug'   => $permission->permission_modules->slug ?? 'N/A',
//                    'name'  => $permission->name,
//                    'slug'  => $permission->slug,
//                    'column_status' => $permission->column_status
//                ];
//            }
//        }
//
//        $permission_modules  = PermissionModule::all();
//        $permission_parent_modules = [];
//        $permission_sub_modules = [];
//        $permission_array = [];
//        if(!empty($permission_modules)) {
//            foreach ($permission_modules as $module) {
//                if($module->parent_id == 0) {
//                    $permission_parent_modules[] = $module;
//                }
//
//                if($module->parent_id != 0) {
//                    $permission_sub_modules[$module->parent_id][] = $module;
//                }
//                $permission_array[$module->id] = Permission::getPermissionByModuleId($module->id);
//            }
//        }
//
//        $all_permissions    = Permission::all()->map(function ($permission){
//            return $permission->id;
//        });
//
//        $return_data    = [
//            'role'  => new RoleResource($role),
//            'role_module_permissions'   => $role_module_permissions,
//            'permission_modules' => $permission_modules,
//            'permission_parent_modules' => $permission_parent_modules,
//            'permission_sub_modules'    => $permission_sub_modules,
//            'permissions'       => $permission_array,
//            'all_permissions'   => $all_permissions
//        ];
//
//        return $this->sendResponse($return_data, 'Role retrieved successfully');
//    }


    public function rolePermissionGet($id)
    {
        /** @var Role $role */
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $role_resource = (object) [
            'id'    => $role->id,
            'name'  => $role->name,
            'description'  => $role->description,
            'guard_name' => $role->guard_name,
            'created_at'    => $role->created_at,
            'updated_at'    => $role->updated_at,
            'role_permissions'   => $role->permissions->map(function ($item){
                return $item->id;
            }),
//            'total_module_permission'   => $role->permissions->select('module_id')->groupBy('module_id'),
            'total_module_permission'   => $role->permissions->groupBy('module_id')->map(function ($module) {
                return $module->count();
            }),
        ];
//        return $role_resource;

        $parent_modules = PermissionModule::where('parent_id', 0)->orderBy('menu_order', 'ASC')->get();
        $sub_module_array = [];
        $permission_array = [];
        foreach($parent_modules as $module) {
            foreach ($module->sub_modules as $sub_module) {
                $sub_module_array[$module->id][] = $sub_module->id;
                $permission_array[$sub_module->id]  = Permission::getPermissionByModuleId($sub_module->id);
            }
            $permission_array[$module->id]  = Permission::getPermissionByModuleId($module->id);

        }

        $module_permissions = [];
        $sub_module_permissions = [];
        $sub_module_count_array = [];
        if($role_resource->total_module_permission) {
            foreach($role_resource->total_module_permission as $key=>$val) {
                $module = PermissionModule::where('id', $key)->first();
                $module_permission_count = $module->permissions ? $module->permissions->count(): 0;
                $role_permission_count = $val;
                if($module_permission_count == $role_permission_count && $role_permission_count > 0 && $module->parent_id == 0) {
                    $module_permissions[] = $key;
                }
                elseif($module_permission_count == $role_permission_count && $role_permission_count > 0) {
                    $sub_module_permissions[] = $key;
                    $sub_module_count_array[$module->parent_id][] = $key;
                    if((count($sub_module_count_array[$module->parent_id]) == count($sub_module_array[$module->parent_id])) && !in_array($module->parent_id, $module_permissions)) {
                        $module_permissions[] = $module->parent_id;
                    }
                }

            }
        }

        $menu_and_permissions_data = $parent_modules->map(function ($item) {
            return [
                'id'    => $item->id,
                'name'  => $item->name,
                'slug'  => $item->slug,
                'parent_id' => $item->parent_id,
                'icon_name' => $item->icon_name,
                'menu_order'    => $item->menu_order,
                'is_children'   => $item->is_children,
                'is_action_menu'    => $item->is_action_menu,
                'is_multiple_action'    => $item->is_multiple_action,
                'total_actions' => $item->total_actions,
                'sub_modules'   => $item->sub_modules->map(function ($sub_item) {
                    return [
                        'id'    => $sub_item->id,
                        'name'  => $sub_item->name,
                        'slug'  => $sub_item->slug,
                        'parent_id' => $sub_item->parent_id,
                        'icon_name' => $sub_item->icon_name,
                        'menu_order'    => $sub_item->menu_order,
                        'is_children'   => $sub_item->is_children,
                        'is_action_menu'    => $sub_item->is_action_menu,
                        'is_multiple_action'    => $sub_item->is_multiple_action,
                        'total_actions' => $sub_item->total_actions,
                        'actions'   => $sub_item->permissions->map(function($action_data) {
                            return [
                                "id" => $action_data->id,
                                "module_id" => $action_data->module_id,
                                "name" => $action_data->name,
                                "slug" => $action_data->slug,
                                "url_path" => $action_data->url_path,
                                "component_path" => $action_data->component_path,
                                "backend_url" => $action_data->backend_url,
                                "frontend_url" => $action_data->frontend_url,
                                "column_status" => $action_data->column_status,
                                "is_nav" => $action_data->is_nav,
                                "is_index" => $action_data->is_index,
                            ];
                        }),
                    ];
                }),
                'actions'   => $item->permissions->map(function($action_data) {
                    return [
                        "id" => $action_data->id,
                        "module_id" => $action_data->module_id,
                        "name" => $action_data->name,
                        "slug" => $action_data->slug,
                        "url_path" => $action_data->url_path,
                        "component_path" => $action_data->component_path,
                        "backend_url" => $action_data->backend_url,
                        "frontend_url" => $action_data->frontend_url,
                        "column_status" => $action_data->column_status,
                        "is_nav" => $action_data->is_nav,
                        "is_index" => $action_data->is_index,
                    ];
                }),
                'action_count'  => $item->permissions->count()
            ];
        });


        $return_data    = [
            'role'  => $role_resource,
            'sub_modules'       => $sub_module_array,
            'permissions'       => $permission_array,
            'module_permissions'    => $module_permissions,
            'sub_module_permissions'    => $sub_module_permissions,
            'menu_and_permissions'   => $menu_and_permissions_data,
        ];

        return $this->sendResponse($return_data, 'Role retrieved successfully');
    }


    /** Role Permission Set */
    public function rolePermissionSet(Request $request, $id)
    {
        // return $request->all();
        /** @var Role $role */
        $role = $this->roleRepository->find($id);
        if (empty($role)) {
            return $this->sendError('Role not found');
        }

        $permission_set = $role->syncPermissions($request->input('role_permissions'));

        return $this->sendSuccess('Role Permissions set successfully');
    }
}
