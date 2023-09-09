<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductImagesAPIRequest;
use App\Http\Requests\API\UpdateProductImagesAPIRequest;
use App\Models\ProductImages;
use App\Repositories\ProductImagesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductImagesController
 * @package App\Http\Controllers\API
 */

class ProductImagesAPIController extends AppBaseController
{
    /** @var  ProductImagesRepository */
    private $productImagesRepository;

    public function __construct(ProductImagesRepository $productImagesRepo)
    {
        $this->productImagesRepository = $productImagesRepo;
    }

    /**
     * Display a listing of the ProductImages.
     * GET|HEAD /productImages
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productImages = $this->productImagesRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productImages->toArray(), 'Product Images retrieved successfully');
    }

    /**
     * Store a newly created ProductImages in storage.
     * POST /productImages
     *
     * @param CreateProductImagesAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductImagesAPIRequest $request)
    {
        $input = $request->all();

        $productImages = $this->productImagesRepository->create($input);

        return $this->sendResponse($productImages->toArray(), 'Product Images saved successfully');
    }

    /**
     * Display the specified ProductImages.
     * GET|HEAD /productImages/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductImages $productImages */
        $productImages = $this->productImagesRepository->find($id);

        if (empty($productImages)) {
            return $this->sendError('Product Images not found');
        }

        return $this->sendResponse($productImages->toArray(), 'Product Images retrieved successfully');
    }

    /**
     * Update the specified ProductImages in storage.
     * PUT/PATCH /productImages/{id}
     *
     * @param int $id
     * @param UpdateProductImagesAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductImagesAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductImages $productImages */
        $productImages = $this->productImagesRepository->find($id);

        if (empty($productImages)) {
            return $this->sendError('Product Images not found');
        }

        $productImages = $this->productImagesRepository->update($input, $id);

        return $this->sendResponse($productImages->toArray(), 'ProductImages updated successfully');
    }

    /**
     * Remove the specified ProductImages from storage.
     * DELETE /productImages/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductImages $productImages */
        $productImages = $this->productImagesRepository->find($id);

        if (empty($productImages)) {
            return $this->sendError('Product Images not found');
        }

        $productImages->delete();

        return $this->sendSuccess('Product Images deleted successfully');
    }
}
