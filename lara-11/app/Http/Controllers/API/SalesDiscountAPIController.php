<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesDiscountAPIRequest;
use App\Http\Requests\API\UpdateSalesDiscountAPIRequest;
use App\Models\SalesDiscount;
use App\Repositories\SalesDiscountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class SalesDiscountController
 * @package App\Http\Controllers\API
 */

class SalesDiscountAPIController extends AppBaseController
{
    /** @var  SalesDiscountRepository */
    private $salesDiscountRepository;

    public function __construct(SalesDiscountRepository $salesDiscountRepo)
    {
        $this->salesDiscountRepository = $salesDiscountRepo;
    }

    /**
     * Display a listing of the SalesDiscount.
     * GET|HEAD /salesDiscounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $salesDiscounts = $this->salesDiscountRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($salesDiscounts->toArray(), 'Sales Discounts retrieved successfully');
    }

    /**
     * Store a newly created SalesDiscount in storage.
     * POST /salesDiscounts
     *
     * @param CreateSalesDiscountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSalesDiscountAPIRequest $request)
    {
        $input = $request->all();

        $salesDiscount = $this->salesDiscountRepository->create($input);

        return $this->sendResponse($salesDiscount->toArray(), 'Sales Discount saved successfully');
    }

    /**
     * Display the specified SalesDiscount.
     * GET|HEAD /salesDiscounts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SalesDiscount $salesDiscount */
        $salesDiscount = $this->salesDiscountRepository->find($id);

        if (empty($salesDiscount)) {
            return $this->sendError('Sales Discount not found');
        }

        return $this->sendResponse($salesDiscount->toArray(), 'Sales Discount retrieved successfully');
    }

    /**
     * Update the specified SalesDiscount in storage.
     * PUT/PATCH /salesDiscounts/{id}
     *
     * @param int $id
     * @param UpdateSalesDiscountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSalesDiscountAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalesDiscount $salesDiscount */
        $salesDiscount = $this->salesDiscountRepository->find($id);

        if (empty($salesDiscount)) {
            return $this->sendError('Sales Discount not found');
        }

        $salesDiscount = $this->salesDiscountRepository->update($input, $id);

        return $this->sendResponse($salesDiscount->toArray(), 'SalesDiscount updated successfully');
    }

    /**
     * Remove the specified SalesDiscount from storage.
     * DELETE /salesDiscounts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SalesDiscount $salesDiscount */
        $salesDiscount = $this->salesDiscountRepository->find($id);

        if (empty($salesDiscount)) {
            return $this->sendError('Sales Discount not found');
        }

        $salesDiscount->delete();

        return $this->sendSuccess('Sales Discount deleted successfully');
    }
}
