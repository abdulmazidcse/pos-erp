<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSaleReturnItemAPIRequest;
use App\Http\Requests\API\UpdateSaleReturnItemAPIRequest;
use App\Models\SaleReturnItem;
use App\Repositories\SaleReturnItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class SaleReturnItemController
 * @package App\Http\Controllers\API
 */

class SaleReturnItemAPIController extends AppBaseController
{
    /** @var  SaleReturnItemRepository */
    private $saleReturnItemRepository;

    public function __construct(SaleReturnItemRepository $saleReturnItemRepo)
    {
        $this->saleReturnItemRepository = $saleReturnItemRepo;
    }

    /**
     * Display a listing of the SaleReturnItem.
     * GET|HEAD /saleReturnItems
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $saleReturnItems = $this->saleReturnItemRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($saleReturnItems->toArray(), 'Sale Return Items retrieved successfully');
    }

    /**
     * Store a newly created SaleReturnItem in storage.
     * POST /saleReturnItems
     *
     * @param CreateSaleReturnItemAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSaleReturnItemAPIRequest $request)
    {
        $input = $request->all();

        $saleReturnItem = $this->saleReturnItemRepository->create($input);

        return $this->sendResponse($saleReturnItem->toArray(), 'Sale Return Item saved successfully');
    }

    /**
     * Display the specified SaleReturnItem.
     * GET|HEAD /saleReturnItems/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SaleReturnItem $saleReturnItem */
        $saleReturnItem = $this->saleReturnItemRepository->find($id);

        if (empty($saleReturnItem)) {
            return $this->sendError('Sale Return Item not found');
        }

        return $this->sendResponse($saleReturnItem->toArray(), 'Sale Return Item retrieved successfully');
    }

    /**
     * Update the specified SaleReturnItem in storage.
     * PUT/PATCH /saleReturnItems/{id}
     *
     * @param int $id
     * @param UpdateSaleReturnItemAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSaleReturnItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var SaleReturnItem $saleReturnItem */
        $saleReturnItem = $this->saleReturnItemRepository->find($id);

        if (empty($saleReturnItem)) {
            return $this->sendError('Sale Return Item not found');
        }

        $saleReturnItem = $this->saleReturnItemRepository->update($input, $id);

        return $this->sendResponse($saleReturnItem->toArray(), 'SaleReturnItem updated successfully');
    }

    /**
     * Remove the specified SaleReturnItem from storage.
     * DELETE /saleReturnItems/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SaleReturnItem $saleReturnItem */
        $saleReturnItem = $this->saleReturnItemRepository->find($id);

        if (empty($saleReturnItem)) {
            return $this->sendError('Sale Return Item not found');
        }

        $saleReturnItem->delete();

        return $this->sendSuccess('Sale Return Item deleted successfully');
    }
}
