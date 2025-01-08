<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductBarcodesAPIRequest;
use App\Http\Requests\API\UpdateProductBarcodesAPIRequest;
use App\Models\ProductBarcodes;
use App\Repositories\ProductBarcodesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductBarcodesController
 * @package App\Http\Controllers\API
 */

class ProductBarcodesAPIController extends AppBaseController
{
    /** @var  ProductBarcodesRepository */
    private $productBarcodesRepository;

    public function __construct(ProductBarcodesRepository $productBarcodesRepo)
    {
        $this->productBarcodesRepository = $productBarcodesRepo;
    }

    /**
     * Display a listing of the ProductBarcodes.
     * GET|HEAD /productBarcodes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productBarcodes = $this->productBarcodesRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productBarcodes->toArray(), 'Product Barcodes retrieved successfully');
    }

    /**
     * Store a newly created ProductBarcodes in storage.
     * POST /productBarcodes
     *
     * @param CreateProductBarcodesAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductBarcodesAPIRequest $request)
    {
        $input = $request->all();

        $productBarcodes = $this->productBarcodesRepository->create($input);

        return $this->sendResponse($productBarcodes->toArray(), 'Product Barcodes saved successfully');
    }

    /**
     * Display the specified ProductBarcodes.
     * GET|HEAD /productBarcodes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductBarcodes $productBarcodes */
        $productBarcodes = $this->productBarcodesRepository->find($id);

        if (empty($productBarcodes)) {
            return $this->sendError('Product Barcodes not found');
        }

        return $this->sendResponse($productBarcodes->toArray(), 'Product Barcodes retrieved successfully');
    }

    /**
     * Update the specified ProductBarcodes in storage.
     * PUT/PATCH /productBarcodes/{id}
     *
     * @param int $id
     * @param UpdateProductBarcodesAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductBarcodesAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductBarcodes $productBarcodes */
        $productBarcodes = $this->productBarcodesRepository->find($id);

        if (empty($productBarcodes)) {
            return $this->sendError('Product Barcodes not found');
        }

        $productBarcodes = $this->productBarcodesRepository->update($input, $id);

        return $this->sendResponse($productBarcodes->toArray(), 'ProductBarcodes updated successfully');
    }

    /**
     * Remove the specified ProductBarcodes from storage.
     * DELETE /productBarcodes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductBarcodes $productBarcodes */
        $productBarcodes = $this->productBarcodesRepository->find($id);

        if (empty($productBarcodes)) {
            return $this->sendError('Product Barcodes not found');
        }

        $productBarcodes->delete();

        return $this->sendSuccess('Product Barcodes deleted successfully');
    }
}
