<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductSupplierAPIRequest;
use App\Http\Requests\API\UpdateProductSupplierAPIRequest;
use App\Models\ProductSupplier;
use App\Repositories\ProductSupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductSupplierController
 * @package App\Http\Controllers\API
 */

class ProductSupplierAPIController extends AppBaseController
{
    /** @var  ProductSupplierRepository */
    private $productSupplierRepository;

    public function __construct(ProductSupplierRepository $productSupplierRepo)
    {
        $this->productSupplierRepository = $productSupplierRepo;
    }

    /**
     * Display a listing of the ProductSupplier.
     * GET|HEAD /productSuppliers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productSuppliers = $this->productSupplierRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productSuppliers->toArray(), 'Product Suppliers retrieved successfully');
    }

    /**
     * Store a newly created ProductSupplier in storage.
     * POST /productSuppliers
     *
     * @param CreateProductSupplierAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductSupplierAPIRequest $request)
    {
        $input = $request->all();

        $productSupplier = $this->productSupplierRepository->create($input);

        return $this->sendResponse($productSupplier->toArray(), 'Product Supplier saved successfully');
    }

    /**
     * Display the specified ProductSupplier.
     * GET|HEAD /productSuppliers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductSupplier $productSupplier */
        $productSupplier = $this->productSupplierRepository->find($id);

        if (empty($productSupplier)) {
            return $this->sendError('Product Supplier not found');
        }

        return $this->sendResponse($productSupplier->toArray(), 'Product Supplier retrieved successfully');
    }

    /**
     * Update the specified ProductSupplier in storage.
     * PUT/PATCH /productSuppliers/{id}
     *
     * @param int $id
     * @param UpdateProductSupplierAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductSupplierAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductSupplier $productSupplier */
        $productSupplier = $this->productSupplierRepository->find($id);

        if (empty($productSupplier)) {
            return $this->sendError('Product Supplier not found');
        }

        $productSupplier = $this->productSupplierRepository->update($input, $id);

        return $this->sendResponse($productSupplier->toArray(), 'ProductSupplier updated successfully');
    }

    /**
     * Remove the specified ProductSupplier from storage.
     * DELETE /productSuppliers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductSupplier $productSupplier */
        $productSupplier = $this->productSupplierRepository->find($id);

        if (empty($productSupplier)) {
            return $this->sendError('Product Supplier not found');
        }

        $productSupplier->delete();

        return $this->sendSuccess('Product Supplier deleted successfully');
    }
}
