<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePermissionModuleAPIRequest;
use App\Http\Requests\API\UpdatePermissionModuleAPIRequest;
use App\Http\Resources\PermissionModuleResource;
use App\Models\PermissionModule;
use App\Repositories\PermissionModuleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class PermissionModuleController
 * @package App\Http\Controllers\API
 */

class PermissionModuleAPIController extends AppBaseController
{
    /** @var  PermissionModuleRepository */
    private $permissionModuleRepository;

    public function __construct(PermissionModuleRepository $permissionModuleRepo)
    {
        $this->permissionModuleRepository = $permissionModuleRepo;
    }

    /**
     * Display a listing of the PermissionModule.
     * GET|HEAD /permissionModules
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
//        $permissionModules = $this->permissionModuleRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );

//        $permissionModules = PermissionModule::where('is_children', 0)->orderBy('parent_id', 'ASC')->orderBy('menu_order', 'ASC')->get();

        $permissionModules = PermissionModule::orderBy('parent_id', 'ASC')->orderBy('menu_order', 'ASC')->get();

        $data   = PermissionModuleResource::collection($permissionModules);
        return $this->sendResponse($data, 'Permission Modules retrieved successfully');
    }

    public function getParentsWithoutHasChildren()
    {
        $permissionModules = PermissionModule::where('is_children', 0)->orderBy('parent_id', 'ASC')->orderBy('menu_order', 'ASC')->get();

        $data   = PermissionModuleResource::collection($permissionModules);
        return $this->sendResponse($data, 'Data Retrieve Successfully');
    }

    public function getParentsModule() {

        $permissionModules = PermissionModule::where('is_children', 1)->orderBy('name', 'asc')->get();

        $data   = PermissionModuleResource::collection($permissionModules);
        return $this->sendResponse($data, 'Data Retrieve Successfully');

    }

    /**
     * Store a newly created PermissionModule in storage.
     * POST /permissionModules
     *
     * @param CreatePermissionModuleAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePermissionModuleAPIRequest $request)
    {
        $this->validate($request, [
            'name'  => 'required|unique:permission_modules,name',
        ]);

        $input = $request->all();
        $input['slug']  = strtolower(str_replace(' ', '-', $request->get('name')));
        $permissionModule = $this->permissionModuleRepository->create($input);

        return $this->sendResponse($permissionModule->toArray(), 'Permission Module saved successfully');
    }

    /**
     * Display the specified PermissionModule.
     * GET|HEAD /permissionModules/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PermissionModule $permissionModule */
        $permissionModule = $this->permissionModuleRepository->find($id);

        if (empty($permissionModule)) {
            return $this->sendError('Permission Module not found');
        }

        return $this->sendResponse($permissionModule->toArray(), 'Permission Module retrieved successfully');
    }

    /**
     * Update the specified PermissionModule in storage.
     * PUT/PATCH /permissionModules/{id}
     *
     * @param int $id
     * @param UpdatePermissionModuleAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePermissionModuleAPIRequest $request)
    {
        $this->validate($request, [
            'name'  => 'required|unique:permission_modules,name,'.$id,
        ]);

        $input = $request->all();
        $input['slug']  = strtolower(str_replace(' ', '-', $request->get('name')));
        /** @var PermissionModule $permissionModule */
        $permissionModule = $this->permissionModuleRepository->find($id);

        if (empty($permissionModule)) {
            return $this->sendError('Permission Module not found');
        }

        $permissionModule = $this->permissionModuleRepository->update($input, $id);

        return $this->sendResponse($permissionModule->toArray(), 'PermissionModule updated successfully');
    }

    /**
     * Remove the specified PermissionModule from storage.
     * DELETE /permissionModules/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PermissionModule $permissionModule */
        $permissionModule = $this->permissionModuleRepository->find($id);

        if (empty($permissionModule)) {
            return $this->sendError('Permission Module not found');
        }

        $action_count = $permissionModule->permissions->count();
        if($action_count > 0) {
            return $this->sendError("This Module Can't be delete. You must this module actions assign to another module");
        }

        $permissionModule->delete();

        return $this->sendSuccess('Permission Module deleted successfully');
    }
}
