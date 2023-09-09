<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSaleItemAPIRequest;
use App\Http\Requests\API\UpdateSaleItemAPIRequest;
use App\Models\SaleItem;
use App\Repositories\SaleItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class SaleItemController
 * @package App\Http\Controllers\API
 */

class SaleItemAPIController extends AppBaseController
{
    /** @var  SaleItemRepository */
    private $saleItemRepository;

    public function __construct(SaleItemRepository $saleItemRepo)
    {
        $this->saleItemRepository = $saleItemRepo;
    }

    /**
     * Display a listing of the SaleItem.
     * GET|HEAD /saleItems
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $saleItems = $this->saleItemRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($saleItems->toArray(), 'Sale Items retrieved successfully');
    }

    /**
     * Store a newly created SaleItem in storage.
     * POST /saleItems
     *
     * @param CreateSaleItemAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSaleItemAPIRequest $request)
    {
        $input = $request->all();

        $saleItem = $this->saleItemRepository->create($input);

        return $this->sendResponse($saleItem->toArray(), 'Sale Item saved successfully');
    }

    /**
     * Display the specified SaleItem.
     * GET|HEAD /saleItems/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SaleItem $saleItem */
        $saleItem = $this->saleItemRepository->find($id);

        if (empty($saleItem)) {
            return $this->sendError('Sale Item not found');
        }

        return $this->sendResponse($saleItem->toArray(), 'Sale Item retrieved successfully');
    }

    /**
     * Update the specified SaleItem in storage.
     * PUT/PATCH /saleItems/{id}
     *
     * @param int $id
     * @param UpdateSaleItemAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSaleItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var SaleItem $saleItem */
        $saleItem = $this->saleItemRepository->find($id);

        if (empty($saleItem)) {
            return $this->sendError('Sale Item not found');
        }

        $saleItem = $this->saleItemRepository->update($input, $id);

        return $this->sendResponse($saleItem->toArray(), 'SaleItem updated successfully');
    }

    /**
     * Remove the specified SaleItem from storage.
     * DELETE /saleItems/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SaleItem $saleItem */
        $saleItem = $this->saleItemRepository->find($id);

        if (empty($saleItem)) {
            return $this->sendError('Sale Item not found');
        }

        $saleItem->delete();

        return $this->sendSuccess('Sale Item deleted successfully');
    }
}
