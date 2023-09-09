<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierTypeAPIRequest;
use App\Http\Requests\API\UpdateSupplierTypeAPIRequest;
use App\Models\SupplierType;
use App\Repositories\SupplierTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class SupplierTypeController
 * @package App\Http\Controllers\API
 */

class SupplierTypeAPIController extends AppBaseController
{
    /** @var  SupplierTypeRepository */
    private $supplierTypeRepository;

    public function __construct(SupplierTypeRepository $supplierTypeRepo)
    {
        $this->supplierTypeRepository = $supplierTypeRepo;
    }

    /**
     * Display a listing of the SupplierType.
     * GET|HEAD /supplierTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $supplierTypes = $this->supplierTypeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );


        $return_data = $supplierTypes->map(function ($item) {
            return [
                'id'    => $item->id,
                'name'  => $item->name,
                'status'=> $item->status,
                'status_name' => ($item->status == 1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>',
            ];
        });

        return $this->sendResponse($return_data, 'Supplier Types retrieved successfully');
    }

    /**
     * Store a newly created SupplierType in storage.
     * POST /supplierTypes
     *
     * @param CreateSupplierTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierTypeAPIRequest $request)
    {
        $this->validate($request, [
            'name'  => 'required|unique:supplier_types,name'
        ]);

        $input = $request->all();

        $supplierType = $this->supplierTypeRepository->create($input);

        return $this->sendResponse($supplierType->toArray(), 'Supplier Type saved successfully');
    }

    /**
     * Display the specified SupplierType.
     * GET|HEAD /supplierTypes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierType $supplierType */
        $supplierType = $this->supplierTypeRepository->find($id);

        if (empty($supplierType)) {
            return $this->sendError('Supplier Type not found');
        }

        return $this->sendResponse($supplierType->toArray(), 'Supplier Type retrieved successfully');
    }

    /**
     * Update the specified SupplierType in storage.
     * PUT/PATCH /supplierTypes/{id}
     *
     * @param int $id
     * @param UpdateSupplierTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierTypeAPIRequest $request)
    {
        $this->validate($request, [
            'name'  => 'required|unique:supplier_types,name,'.$id,
        ]);
        $input = $request->all();

        /** @var SupplierType $supplierType */
        $supplierType = $this->supplierTypeRepository->find($id);

        if (empty($supplierType)) {
            return $this->sendError('Supplier Type not found');
        }

        $supplierType = $this->supplierTypeRepository->update($input, $id);

        return $this->sendResponse($supplierType->toArray(), 'SupplierType updated successfully');
    }

    /**
     * Remove the specified SupplierType from storage.
     * DELETE /supplierTypes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierType $supplierType */
        $supplierType = $this->supplierTypeRepository->find($id);

        if (empty($supplierType)) {
            return $this->sendError('Supplier Type not found');
        }

        $supplierType->delete();

        return $this->sendSuccess('Supplier Type deleted successfully');
    }
}
