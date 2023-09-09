<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductSizeAPIRequest;
use App\Http\Requests\API\UpdateProductSizeAPIRequest;
use App\Models\ProductSize;
use App\Repositories\ProductSizeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductSizeController
 * @package App\Http\Controllers\API
 */

class ProductSizeAPIController extends AppBaseController
{
    /** @var  ProductSizeRepository */
    private $productSizeRepository;

    public function __construct(ProductSizeRepository $productSizeRepo)
    {
        $this->productSizeRepository = $productSizeRepo;
    }

    /**
     * Display a listing of the ProductSize.
     * GET|HEAD /productSizes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productSizes = $this->productSizeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productSizes->toArray(), 'Product Sizes retrieved successfully');
    }

    /**
     * Store a newly created ProductSize in storage.
     * POST /productSizes
     *
     * @param CreateProductSizeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductSizeAPIRequest $request)
    {
        $input = $request->all();

        $productSize = $this->productSizeRepository->create($input);

        return $this->sendResponse($productSize->toArray(), 'Product Size saved successfully');
    }

    /**
     * Display the specified ProductSize.
     * GET|HEAD /productSizes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductSize $productSize */
        $productSize = $this->productSizeRepository->find($id);

        if (empty($productSize)) {
            return $this->sendError('Product Size not found');
        }

        return $this->sendResponse($productSize->toArray(), 'Product Size retrieved successfully');
    }

    /**
     * Update the specified ProductSize in storage.
     * PUT/PATCH /productSizes/{id}
     *
     * @param int $id
     * @param UpdateProductSizeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductSizeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductSize $productSize */
        $productSize = $this->productSizeRepository->find($id);

        if (empty($productSize)) {
            return $this->sendError('Product Size not found');
        }

        $productSize = $this->productSizeRepository->update($input, $id);

        return $this->sendResponse($productSize->toArray(), 'ProductSize updated successfully');
    }

    /**
     * Remove the specified ProductSize from storage.
     * DELETE /productSizes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductSize $productSize */
        $productSize = $this->productSizeRepository->find($id);

        if (empty($productSize)) {
            return $this->sendError('Product Size not found');
        }

        $productSize->delete();

        return $this->sendSuccess('Product Size deleted successfully');
    }
}
