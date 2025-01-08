<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductColorAPIRequest;
use App\Http\Requests\API\UpdateProductColorAPIRequest;
use App\Models\ProductColor;
use App\Repositories\ProductColorRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductColorController
 * @package App\Http\Controllers\API
 */

class ProductColorAPIController extends AppBaseController
{
    /** @var  ProductColorRepository */
    private $productColorRepository;

    public function __construct(ProductColorRepository $productColorRepo)
    {
        $this->productColorRepository = $productColorRepo;
    }

    /**
     * Display a listing of the ProductColor.
     * GET|HEAD /productColors
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productColors = $this->productColorRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productColors->toArray(), 'Product Colors retrieved successfully');
    }

    /**
     * Store a newly created ProductColor in storage.
     * POST /productColors
     *
     * @param CreateProductColorAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductColorAPIRequest $request)
    {
        $input = $request->all();

        $productColor = $this->productColorRepository->create($input);

        return $this->sendResponse($productColor->toArray(), 'Product Color saved successfully');
    }

    /**
     * Display the specified ProductColor.
     * GET|HEAD /productColors/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductColor $productColor */
        $productColor = $this->productColorRepository->find($id);

        if (empty($productColor)) {
            return $this->sendError('Product Color not found');
        }

        return $this->sendResponse($productColor->toArray(), 'Product Color retrieved successfully');
    }

    /**
     * Update the specified ProductColor in storage.
     * PUT/PATCH /productColors/{id}
     *
     * @param int $id
     * @param UpdateProductColorAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductColorAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductColor $productColor */
        $productColor = $this->productColorRepository->find($id);

        if (empty($productColor)) {
            return $this->sendError('Product Color not found');
        }

        $productColor = $this->productColorRepository->update($input, $id);

        return $this->sendResponse($productColor->toArray(), 'ProductColor updated successfully');
    }

    /**
     * Remove the specified ProductColor from storage.
     * DELETE /productColors/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductColor $productColor */
        $productColor = $this->productColorRepository->find($id);

        if (empty($productColor)) {
            return $this->sendError('Product Color not found');
        }

        $productColor->delete();

        return $this->sendSuccess('Product Color deleted successfully');
    }
}
