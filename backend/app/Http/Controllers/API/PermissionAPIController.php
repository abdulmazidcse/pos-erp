<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePermissionAPIRequest;
use App\Http\Requests\API\UpdatePermissionAPIRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\PermissionModule;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class PermissionController
 * @package App\Http\Controllers\API
 */

class PermissionAPIController extends AppBaseController
{
    /** @var  PermissionRepository */
    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepo)
    {
        $this->permissionRepository = $permissionRepo;
    }

    /**
     * Display a listing of the Permission.
     * GET|HEAD /permissions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $permissions = $this->permissionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($permissions->toArray(), 'Permissions retrieved successfully');
    }

    public function permissionList(Request $request)
    {
        $columns = ['name', 'slug', 'url_path', 'component_path', 'module_id', 'module_name', 'is_nav', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = Permission::with(['permission_modules'])->orderBy($columns[$column], $dir);

        //return $request->all();
//        return $query->toSql();

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('permission_modules', function ($query2) use ($searchValue) {
                    $query2->where('name', 'like', '%'. $searchValue .'%');
                });
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('slug', 'like', '%' .$searchValue. '%');
                $query->orWhere('url_path', 'like', '%' .$searchValue. '%');
                $query->orWhere('component_path', 'like', '%' .$searchValue. '%');
            });
        }

        $permissions = $query->paginate($length);
        $return_data    = [
//            'data' => PermissionResource::collection($permissions),
            'data' => $permissions,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Districts retrieved successfully');
    }

    /**
     * Store a newly created Permission in storage.
     * POST /permissions
     *
     * @param CreatePermissionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePermissionAPIRequest $request)
    {
        if($request->input('is_route_action') == 1) {
            $required_input = 'required';
        }else{
            $required_input = 'sometimes';
        }
        $this->validate($request, [
            'name'  => 'required',
            'url_path'  => $required_input,
            'component_path'    => $required_input,
        ]);

        $permission_module = PermissionModule::find($request->get('module_id'));
        if(empty($permission_module)) {
            $this->sendError('Permission module do not found!');
        }
        $input = $request->all();
        $slug  = strtolower(str_replace(' ', '-', $request->get('name')));
        $slug_explode = explode('-', $slug);
        $module_slug  = explode('-', $permission_module->slug);
        if(count($module_slug) > 2) {
            $input['slug']  = $module_slug[0].'-'.$module_slug[1].'-'.$slug;
        }else {
            if (strtolower($module_slug[0]) == strtolower($slug_explode[0])) {
                $input['slug'] = $slug;
            } else {
                $input['slug'] = $module_slug[0] . '-' . $slug;
            }
        }

        DB::beginTransaction();
        try{

            $permission = Permission::create($input);
            $permission_count   = Permission::where('module_id', $permission_module->id)->count();
            $module_update  = $permission_module->update(['total_actions' => $permission_count]);

            DB::commit();
            return $this->sendResponse($permission->toArray(), 'Permission saved successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Display the specified Permission.
     * GET|HEAD /permissions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {
            return $this->sendError('Permission not found');
        }

        return $this->sendResponse($permission->toArray(), 'Permission retrieved successfully');
    }

    /**
     * Update the specified Permission in storage.
     * PUT/PATCH /permissions/{id}
     *
     * @param int $id
     * @param UpdatePermissionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePermissionAPIRequest $request)
    {
        if($request->input('is_route_action') == 1) {
            $required_input = 'required';
            $url_path = $request->get('url_path');
            $component_path = $request->get('component_path');
        }else{
            $required_input = 'sometimes';
            $url_path = NULL;
            $component_path = NULL;
        }
        $this->validate($request, [
            'name'  => 'required',
            'url_path'  => $required_input,
            'component_path'    => $required_input,
        ]);

        $input = $request->all();

        $input['url_path']  = $url_path;
        $input['component_path']    = $component_path;

        /** @var Permission $permission */
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {
            return $this->sendError('Permission not found');
        }

        $permission_module = PermissionModule::find($request->get('module_id'));
        if(empty($permission_module)) {
            $this->sendError('Permission module do not found!');
        }

        $slug  = strtolower(str_replace(' ', '-', $request->get('name')));
        $slug_explode = explode('-', $slug);
        $module_slug  = explode('-', $permission_module->slug);
        if(count($module_slug) > 2) {
            $input['slug']  = $module_slug[0].'-'.$module_slug[1].'-'.$slug;
        }else {
            if (strtolower($module_slug[0]) == strtolower($slug_explode[0])) {
                $input['slug'] = $slug;
            } else {
                $input['slug'] = $module_slug[0] . '-' . $slug;
            }
        }

        $permission = $this->permissionRepository->update($input, $id);

        return $this->sendResponse($permission->toArray(), 'Permission updated successfully');
    }

    /**
     * Remove the specified Permission from storage.
     * DELETE /permissions/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Permission $permission */
        $permission = $this->permissionRepository->find($id);

        if (empty($permission)) {
            return $this->sendError('Permission not found');
        }

        $permission->delete();

        return $this->sendSuccess('Permission deleted successfully');
    }

    public function getUserMenuAndRolePermissions() {

        $user = auth()->user();

        $user_all_permissions = $user->getAllPermissions();
        $user_route_list_array = [];
        $menu_index = [];
        $menu_action = [];
        $permission_with_module = [];
        if(!empty($user_all_permissions)) {
            foreach ($user_all_permissions as $permission) {

                if($permission->is_route_action) {
                    $user_route_list_array[] = [
                        'path' => $permission->url_path,
                        'name' => $permission->slug,
                        'component' => $permission->component_path,
                        'module_name'   => $permission->permission_modules ? $permission->permission_modules->name : "",
                        'parent_module' => $permission->permission_modules->parents ? $permission->permission_modules->parents->name : ""
                    ];
                }

                if($permission->is_nav == 1) {
                    $menu_index[$permission->module_id] = $permission;
                }else{
                    $menu_action[$permission->module_id][] = $permission;
                }


                $permission_with_module[$permission->module_id][$permission->slug] = $permission;
            }
        }

        //return $menu_index;
        $parent_modules = PermissionModule::where('parent_id', 0)->orderBy('menu_order', 'ASC')->get();

        $user_navigation_array = [];
        $user_navigation_with_module = [];
        $navigation_with_module = [];
        $module_array = [];
        if($parent_modules) {
            foreach ($parent_modules as $module) {
                $sub_modules_array = [];
                if($module->sub_modules) {
                    $sub_modules = $module->sub_modules()->orderBy('menu_order', 'ASC')->get();
                    foreach ($sub_modules as $msub_module) {

                        if(isset($menu_index[$msub_module->id])) {
                            $sub_modules_array[$module->id][] = $msub_module;
                        }
                    }
                }

                if(isset($menu_index[$module->id]) || count($sub_modules_array) > 0) {
                    $navigation_child_array = [];
                    if ($module->is_action_menu == 1 && $module->is_children == 0 && isset($menu_index[$module->id])) {
                        $path = $menu_index[$module->id]->url_path;
                        $icon_name = $module->icon_name;
                    } else {
                        $path = '/#';
                        $icon_name = $module->icon_name;
                    }

                    if ($module->is_children == 1) {
                        $sub_module_menus = $module->sub_modules()->orderBy('menu_order', 'ASC')->get();
                        foreach ($sub_module_menus as $sub_module) {
                            if (isset($menu_index[$sub_module->id])) {
                                $sub_path = $menu_index[$sub_module->id]->url_path;
                                $sub_icon_name = $sub_module->icon_name;
                                $navigation_child_array[] = [
                                    'path' => $sub_path,
                                    'name' => $sub_module->name,
                                    'icon_name' => $sub_icon_name,
                                ];


                                $navigation_with_module[$sub_path] = $permission_with_module[$sub_module->id];
                            }
                        }
                    }
                    $user_navigation_array[] = [
                        'path' => $path,
                        'name' => $module->name,
                        'icon_name' => $icon_name,
                        'children' => $navigation_child_array
                    ];

                    $user_navigation_with_module[$module->name][] = [
//                        'menu_details'  => $menu_index[$module->id] ?? '',
                        "name"=> $menu_index[$module->id]->name ?? "",
                        "slug"=> $menu_index[$module->id]->slug ?? "",
                        "url_path"=> $menu_index[$module->id]->url_path ?? "#",
                        "component_path"=> $menu_index[$module->id]->component_path ?? "",
                        "is_route_action"=> $menu_index[$module->id]->is_route_action ?? "",
                        "is_nav"=> $menu_index[$module->id]->is_nav ?? "",
                        "is_index"=> $menu_index[$module->id]->is_index ?? "",
                        'path' => $path,
                        'module_name' => $module->name,
                        'icon_name' => $icon_name,
                        'children' => $navigation_with_module,
                        'other_actions' => ""
                    ];
                }

                if(isset($menu_action[$module->id])){
                    if ($module->is_action_menu == 1 && $module->is_children == 0 && isset($menu_action[$module->id])) {
                        $path = $menu_index[$module->id]->url_path;
                        $icon_name = $module->icon_name;
                    } else {
                        $path = '/#';
                        $icon_name = $module->icon_name;
                    }

                    $sub_actions_array  = [];
                    for($a=0; $a<count($menu_action[$module->id]); $a++) {
                        $sub_actions_array[$menu_action[$module->id][$a]->slug] = [
//                        'menu_details'  => $menu_action[$module->id] ?? '',
                            "name"=> $menu_action[$module->id][$a]->name ?? "",
                            "slug"=> $menu_action[$module->id][$a]->slug ?? "",
                            "url_path"=> $menu_action[$module->id][$a]->url_path ?? "#",
                            "component_path"=> $menu_action[$module->id][$a]->component_path ?? "",
                            "is_route_action"=> $menu_action[$module->id][$a]->is_route_action ?? "",
                            "is_nav"=> $menu_action[$module->id][$a]->is_nav ?? "",
                            "is_index"=> $menu_action[$module->id][$a]->is_index ?? "",
                            'path' => $path,
                            'module_name' => $module->name,
                            'icon_name' => $icon_name,
                            'children' => [],
                        ];
                    }
                    $user_navigation_with_module[$module->name][0]['other_actions'] = $sub_actions_array;
//                    array_merge($user_navigation_with_module[$module->name], $menu_action[$module->id]);
//                    $user_navigation_with_module[$module->name][] = $menu_action[$module->id];
                }

                $module_array[] = $module->id;
            }
        }

        $return_data    = [
            'user_all_permissions'  => $user_all_permissions,
            'user_routes'   => $user_route_list_array,
            'user_navigations'  => $user_navigation_array,
            'data_check'    => 1,
            'module_based_navigation'   => $user_navigation_with_module,
//            'module_array'   => $module_array,
//            'menu_index'    => $menu_index,
//            'menu_action'    => $menu_action['1'],
//            'sub_modules'   => $sub_modules_array,
//            'sub_modules_menus'   => $sub_module_menus,
//            'permission_with_module'  => $permission_with_module,
        ];
        return $this->sendResponse($return_data, 'Data Retrieve Successfully');

    }

    public function getUserMenuAndRolePermissionsBackup() {

        $user = auth()->user();

        $user_all_permissions = $user->getAllPermissions();
        $user_route_list_array = [];
        $menu_index = [];
        if(!empty($user_all_permissions)) {
            foreach ($user_all_permissions as $permission) {

                if($permission->is_route_action) {
                    $user_route_list_array[] = [
                        'path' => $permission->url_path,
                        'name' => $permission->slug,
                        'component' => $permission->component_path
                    ];
                }

                if($permission->is_nav == 1) {
                    $menu_index[$permission->module_id] = $permission;
                }


            }
        }

        //return $menu_index;
        $parent_modules = PermissionModule::where('parent_id', 0)->orderBy('menu_order', 'ASC')->get();

        $user_navigation_array = [];
        if($parent_modules) {
            foreach ($parent_modules as $module) {
                $sub_modules_array = [];
                if($module->sub_modules) {
                    $sub_modules = $module->sub_modules()->orderBy('menu_order', 'ASC')->get();
                    foreach ($sub_modules as $msub_module) {

                        if(isset($menu_index[$msub_module->id])) {
                            $sub_modules_array[$module->id][] = $msub_module;
                        }
                    }
                }

                if(isset($menu_index[$module->id]) || count($sub_modules_array) > 0) {
                    $navigation_child_array = [];
                    if ($module->is_action_menu == 1 && $module->is_children == 0 && isset($menu_index[$module->id])) {
                        $path = $menu_index[$module->id]->url_path;
                        $icon_name = $module->icon_name;
                    } else {
                        $path = '/#';
                        $icon_name = $module->icon_name;
                    }

                    if ($module->is_children == 1) {
                        $sub_module_menus = $module->sub_modules()->orderBy('menu_order', 'ASC')->get();
                        foreach ($sub_module_menus as $sub_module) {
                            if (isset($menu_index[$sub_module->id])) {
                                $sub_path = $menu_index[$sub_module->id]->url_path;
                                $sub_icon_name = $sub_module->icon_name;
                                $navigation_child_array[] = [
                                    'path' => $sub_path,
                                    'name' => $sub_module->name,
                                    'icon_name' => $sub_icon_name,
                                ];
                            }
                        }
                    }
                    $user_navigation_array[] = [
                        'path' => $path,
                        'name' => $module->name,
                        'icon_name' => $icon_name,
                        'children' => $navigation_child_array
                    ];
                }
            }
        }

        $return_data    = [
            'user_all_permissions'  => $user_all_permissions,
            'user_routes'   => $user_route_list_array,
            'user_navigations'  => $user_navigation_array,
            'data_check'    => 1,
        ];
        return $this->sendResponse($return_data, 'Data Retrieve Successfully');

    }
}
