<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCouponAPIRequest;
use App\Http\Requests\API\UpdateCouponAPIRequest;
use App\Models\Coupon;
use App\Repositories\CouponRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class CouponController
 * @package App\Http\Controllers\API
 */

class CouponAPIController extends AppBaseController
{
    /** @var  CouponRepository */
    private $couponRepository;

    public function __construct(CouponRepository $couponRepo)
    {
        $this->couponRepository = $couponRepo;
    }

    /**
     * Display a listing of the Coupon.
     * GET|HEAD /coupons
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $coupons = $this->couponRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($coupons->toArray(), 'Coupons retrieved successfully');
    }

    /**
     * Store a newly created Coupon in storage.
     * POST /coupons
     *
     * @param CreateCouponAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCouponAPIRequest $request)
    {
        $this->validate($request, [
            'code'      => 'required|unique:coupons,code',
            'discount_amount'      => 'required', 
        ], [
            'code.unique' => 'This Coupon Code Already Exists',
        ]);
        $input = $request->all();

        $coupon = $this->couponRepository->create($input);

        return $this->sendResponse($coupon->toArray(), 'Coupon saved successfully');
    }

    /**
     * Display the specified Coupon.
     * GET|HEAD /coupons/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Coupon $coupon */
        $coupon = $this->couponRepository->find($id);

        if (empty($coupon)) {
            return $this->sendError('Coupon not found');
        }

        return $this->sendResponse($coupon->toArray(), 'Coupon retrieved successfully');
    }

    /**
     * Update the specified Coupon in storage.
     * PUT/PATCH /coupons/{id}
     *
     * @param int $id
     * @param UpdateCouponAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCouponAPIRequest $request)
    {
        $input = $request->all();

        /** @var Coupon $coupon */
        $coupon = $this->couponRepository->find($id);

        if (empty($coupon)) {
            return $this->sendError('Coupon not found');
        }

        $coupon = $this->couponRepository->update($input, $id);

        return $this->sendResponse($coupon->toArray(), 'Coupon updated successfully');
    }

    /**
     * Remove the specified Coupon from storage.
     * DELETE /coupons/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Coupon $coupon */
        $coupon = $this->couponRepository->find($id);

        if (empty($coupon)) {
            return $this->sendError('Coupon not found');
        }

        $coupon->delete();

        return $this->sendSuccess('Coupon deleted successfully');
    }
}
