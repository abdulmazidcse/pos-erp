<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHoldSaleDiscountAPIRequest;
use App\Http\Requests\API\UpdateHoldSaleDiscountAPIRequest;
use App\Models\HoldSaleDiscount;
use App\Repositories\HoldSaleDiscountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class HoldSaleDiscountController
 * @package App\Http\Controllers\API
 */

class HoldSaleDiscountAPIController extends AppBaseController
{
    /** @var  HoldSaleDiscountRepository */
    private $holdSaleDiscountRepository;

    public function __construct(HoldSaleDiscountRepository $holdSaleDiscountRepo)
    {
        $this->holdSaleDiscountRepository = $holdSaleDiscountRepo;
    }

    /**
     * Display a listing of the HoldSaleDiscount.
     * GET|HEAD /holdSaleDiscounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $holdSaleDiscounts = $this->holdSaleDiscountRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($holdSaleDiscounts->toArray(), 'Hold Sale Discounts retrieved successfully');
    }

    /**
     * Store a newly created HoldSaleDiscount in storage.
     * POST /holdSaleDiscounts
     *
     * @param CreateHoldSaleDiscountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateHoldSaleDiscountAPIRequest $request)
    {
        $input = $request->all();

        $holdSaleDiscount = $this->holdSaleDiscountRepository->create($input);

        return $this->sendResponse($holdSaleDiscount->toArray(), 'Hold Sale Discount saved successfully');
    }

    /**
     * Display the specified HoldSaleDiscount.
     * GET|HEAD /holdSaleDiscounts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var HoldSaleDiscount $holdSaleDiscount */
        $holdSaleDiscount = $this->holdSaleDiscountRepository->find($id);

        if (empty($holdSaleDiscount)) {
            return $this->sendError('Hold Sale Discount not found');
        }

        return $this->sendResponse($holdSaleDiscount->toArray(), 'Hold Sale Discount retrieved successfully');
    }

    /**
     * Update the specified HoldSaleDiscount in storage.
     * PUT/PATCH /holdSaleDiscounts/{id}
     *
     * @param int $id
     * @param UpdateHoldSaleDiscountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHoldSaleDiscountAPIRequest $request)
    {
        $input = $request->all();

        /** @var HoldSaleDiscount $holdSaleDiscount */
        $holdSaleDiscount = $this->holdSaleDiscountRepository->find($id);

        if (empty($holdSaleDiscount)) {
            return $this->sendError('Hold Sale Discount not found');
        }

        $holdSaleDiscount = $this->holdSaleDiscountRepository->update($input, $id);

        return $this->sendResponse($holdSaleDiscount->toArray(), 'HoldSaleDiscount updated successfully');
    }

    /**
     * Remove the specified HoldSaleDiscount from storage.
     * DELETE /holdSaleDiscounts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var HoldSaleDiscount $holdSaleDiscount */
        $holdSaleDiscount = $this->holdSaleDiscountRepository->find($id);

        if (empty($holdSaleDiscount)) {
            return $this->sendError('Hold Sale Discount not found');
        }

        $holdSaleDiscount->delete();

        return $this->sendSuccess('Hold Sale Discount deleted successfully');
    }
}
