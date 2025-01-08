<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHoldSaleItemAPIRequest;
use App\Http\Requests\API\UpdateHoldSaleItemAPIRequest;
use App\Models\HoldSaleItem;
use App\Repositories\HoldSaleItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class HoldSaleItemController
 * @package App\Http\Controllers\API
 */

class HoldSaleItemAPIController extends AppBaseController
{
    /** @var  HoldSaleItemRepository */
    private $holdSaleItemRepository;

    public function __construct(HoldSaleItemRepository $holdSaleItemRepo)
    {
        $this->holdSaleItemRepository = $holdSaleItemRepo;
    }

    /**
     * Display a listing of the HoldSaleItem.
     * GET|HEAD /holdSaleItems
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $holdSaleItems = $this->holdSaleItemRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($holdSaleItems->toArray(), 'Hold Sale Items retrieved successfully');
    }

    /**
     * Store a newly created HoldSaleItem in storage.
     * POST /holdSaleItems
     *
     * @param CreateHoldSaleItemAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateHoldSaleItemAPIRequest $request)
    {
        $input = $request->all();

        $holdSaleItem = $this->holdSaleItemRepository->create($input);

        return $this->sendResponse($holdSaleItem->toArray(), 'Hold Sale Item saved successfully');
    }

    /**
     * Display the specified HoldSaleItem.
     * GET|HEAD /holdSaleItems/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var HoldSaleItem $holdSaleItem */
        $holdSaleItem = $this->holdSaleItemRepository->find($id);

        if (empty($holdSaleItem)) {
            return $this->sendError('Hold Sale Item not found');
        }

        return $this->sendResponse($holdSaleItem->toArray(), 'Hold Sale Item retrieved successfully');
    }

    /**
     * Update the specified HoldSaleItem in storage.
     * PUT/PATCH /holdSaleItems/{id}
     *
     * @param int $id
     * @param UpdateHoldSaleItemAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHoldSaleItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var HoldSaleItem $holdSaleItem */
        $holdSaleItem = $this->holdSaleItemRepository->find($id);

        if (empty($holdSaleItem)) {
            return $this->sendError('Hold Sale Item not found');
        }

        $holdSaleItem = $this->holdSaleItemRepository->update($input, $id);

        return $this->sendResponse($holdSaleItem->toArray(), 'HoldSaleItem updated successfully');
    }

    /**
     * Remove the specified HoldSaleItem from storage.
     * DELETE /holdSaleItems/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var HoldSaleItem $holdSaleItem */
        $holdSaleItem = $this->holdSaleItemRepository->find($id);

        if (empty($holdSaleItem)) {
            return $this->sendError('Hold Sale Item not found');
        }

        $holdSaleItem->delete();

        return $this->sendSuccess('Hold Sale Item deleted successfully');
    }
}
