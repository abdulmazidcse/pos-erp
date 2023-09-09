<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseAPIRequest;
use App\Http\Requests\API\UpdateWarehouseAPIRequest;
use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class WarehouseController
 * @package App\Http\Controllers\API
 */

class WarehouseAPIController extends AppBaseController
{
    /** @var  WarehouseRepository */
    private $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepo)
    {
        $this->warehouseRepository = $warehouseRepo;
    }

    /**
     * Display a listing of the Warehouse.
     * GET|HEAD /warehouses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $warehouses = $this->warehouseRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($warehouses->toArray(), 'Warehouses retrieved successfully');
    }

    /**
     * Store a newly created Warehouse in storage.
     * POST /warehouses
     *
     * @param CreateWarehouseAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateWarehouseAPIRequest $request)
    {
        $this->validate($request, [
            'company_id'    => 'required',
            'name'          => 'required|unique:warehouses,name,NULL,id,company_id,'.$request->company_id, 
            'contact_person_name' => 'required',
            'warehouse_number' => 'required', 
            'address' => 'required',
        ],
        [
            'name.required'   => 'The outlet name field is required',
            'company_id.required'   => 'The company field is required',
        ]);
        $input = $request->all(); 
        $warehouse = $this->warehouseRepository->create($input);

        return $this->sendResponse($warehouse->toArray(), 'Warehouse saved successfully');
    }

    /**
     * Display the specified Warehouse.
     * GET|HEAD /warehouses/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->warehouseRepository->find($id);

        if (empty($warehouse)) {
            return $this->sendError('Warehouse not found');
        }

        return $this->sendResponse($warehouse->toArray(), 'Warehouse retrieved successfully');
    }

    /**
     * Update the specified Warehouse in storage.
     * PUT/PATCH /warehouses/{id}
     *
     * @param int $id
     * @param UpdateWarehouseAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWarehouseAPIRequest $request)
    {
        $input = $request->all();

        /** @var Warehouse $warehouse */
        $warehouse = $this->warehouseRepository->find($id);

        if (empty($warehouse)) {
            return $this->sendError('Warehouse not found');
        }

        $warehouse = $this->warehouseRepository->update($input, $id);

        return $this->sendResponse($warehouse->toArray(), 'Warehouse updated successfully');
    }

    /**
     * Remove the specified Warehouse from storage.
     * DELETE /warehouses/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->warehouseRepository->find($id);

        if (empty($warehouse)) {
            return $this->sendError('Warehouse not found');
        }

        $warehouse->delete();

        return $this->sendSuccess('Warehouse deleted successfully');
    }
}
