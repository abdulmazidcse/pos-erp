<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductGroupAPIRequest;
use App\Http\Requests\API\UpdateProductGroupAPIRequest;
use App\Models\ProductGroup;
use App\Repositories\ProductGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductGroupController
 * @package App\Http\Controllers\API
 */

class ProductGroupAPIController extends AppBaseController
{
    /** @var  ProductGroupRepository */
    private $productGroupRepository;

    public function __construct(ProductGroupRepository $productGroupRepo)
    {
        $this->productGroupRepository = $productGroupRepo;
    }

    /**
     * Display a listing of the ProductGroup.
     * GET|HEAD /productGroups
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productGroups = $this->productGroupRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productGroups->toArray(), 'Product Groups retrieved successfully');
    }

    /**
     * Store a newly created ProductGroup in storage.
     * POST /productGroups
     *
     * @param CreateProductGroupAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductGroupAPIRequest $request)
    {
        $input = $request->all();

        $productGroup = $this->productGroupRepository->create($input);

        return $this->sendResponse($productGroup->toArray(), 'Product Group saved successfully');
    }

    /**
     * Display the specified ProductGroup.
     * GET|HEAD /productGroups/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductGroup $productGroup */
        $productGroup = $this->productGroupRepository->find($id);

        if (empty($productGroup)) {
            return $this->sendError('Product Group not found');
        }

        return $this->sendResponse($productGroup->toArray(), 'Product Group retrieved successfully');
    }

    /**
     * Update the specified ProductGroup in storage.
     * PUT/PATCH /productGroups/{id}
     *
     * @param int $id
     * @param UpdateProductGroupAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductGroup $productGroup */
        $productGroup = $this->productGroupRepository->find($id);

        if (empty($productGroup)) {
            return $this->sendError('Product Group not found');
        }

        $productGroup = $this->productGroupRepository->update($input, $id);

        return $this->sendResponse($productGroup->toArray(), 'ProductGroup updated successfully');
    }

    /**
     * Remove the specified ProductGroup from storage.
     * DELETE /productGroups/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductGroup $productGroup */
        $productGroup = $this->productGroupRepository->find($id);

        if (empty($productGroup)) {
            return $this->sendError('Product Group not found');
        }

        $productGroup->delete();

        return $this->sendSuccess('Product Group deleted successfully');
    }
}
