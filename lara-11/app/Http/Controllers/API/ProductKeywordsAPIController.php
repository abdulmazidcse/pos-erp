<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductKeywordsAPIRequest;
use App\Http\Requests\API\UpdateProductKeywordsAPIRequest;
use App\Models\ProductKeywords;
use App\Repositories\ProductKeywordsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ProductKeywordsController
 * @package App\Http\Controllers\API
 */

class ProductKeywordsAPIController extends AppBaseController
{
    /** @var  ProductKeywordsRepository */
    private $productKeywordsRepository;

    public function __construct(ProductKeywordsRepository $productKeywordsRepo)
    {
        $this->productKeywordsRepository = $productKeywordsRepo;
    }

    /**
     * Display a listing of the ProductKeywords.
     * GET|HEAD /productKeywords
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productKeywords = $this->productKeywordsRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($productKeywords->toArray(), 'Product Keywords retrieved successfully');
    }

    /**
     * Store a newly created ProductKeywords in storage.
     * POST /productKeywords
     *
     * @param CreateProductKeywordsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductKeywordsAPIRequest $request)
    {
        $input = $request->all();

        $productKeywords = $this->productKeywordsRepository->create($input);

        return $this->sendResponse($productKeywords->toArray(), 'Product Keywords saved successfully');
    }

    /**
     * Display the specified ProductKeywords.
     * GET|HEAD /productKeywords/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductKeywords $productKeywords */
        $productKeywords = $this->productKeywordsRepository->find($id);

        if (empty($productKeywords)) {
            return $this->sendError('Product Keywords not found');
        }

        return $this->sendResponse($productKeywords->toArray(), 'Product Keywords retrieved successfully');
    }

    /**
     * Update the specified ProductKeywords in storage.
     * PUT/PATCH /productKeywords/{id}
     *
     * @param int $id
     * @param UpdateProductKeywordsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductKeywordsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductKeywords $productKeywords */
        $productKeywords = $this->productKeywordsRepository->find($id);

        if (empty($productKeywords)) {
            return $this->sendError('Product Keywords not found');
        }

        $productKeywords = $this->productKeywordsRepository->update($input, $id);

        return $this->sendResponse($productKeywords->toArray(), 'ProductKeywords updated successfully');
    }

    /**
     * Remove the specified ProductKeywords from storage.
     * DELETE /productKeywords/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductKeywords $productKeywords */
        $productKeywords = $this->productKeywordsRepository->find($id);

        if (empty($productKeywords)) {
            return $this->sendError('Product Keywords not found');
        }

        $productKeywords->delete();

        return $this->sendSuccess('Product Keywords deleted successfully');
    }
}
