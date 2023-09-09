<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDiscountSettingAPIRequest;
use App\Http\Requests\API\UpdateDiscountSettingAPIRequest;
use App\Models\DiscountSetting;
use App\Repositories\DiscountSettingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class DiscountSettingController
 * @package App\Http\Controllers\API
 */

class DiscountSettingAPIController extends AppBaseController
{
    /** @var  DiscountSettingRepository */
    private $discountSettingRepository;

    public function __construct(DiscountSettingRepository $discountSettingRepo)
    {
        $this->discountSettingRepository = $discountSettingRepo;
    }

    /**
     * Display a listing of the DiscountSetting.
     * GET|HEAD /discountSettings
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $discountSettings = $this->discountSettingRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($discountSettings->toArray(), 'Discount Settings retrieved successfully');
    }

    /**
     * Store a newly created DiscountSetting in storage.
     * POST /discountSettings
     *
     * @param CreateDiscountSettingAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDiscountSettingAPIRequest $request)
    {
        $input = $request->all();

        $discountSetting = $this->discountSettingRepository->create($input);

        return $this->sendResponse($discountSetting->toArray(), 'Discount setting saved successfully');
    }

    /**
     * Display the specified DiscountSetting.
     * GET|HEAD /discountSettings/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DiscountSetting $discountSetting */
        $discountSetting = $this->discountSettingRepository->find($id);

        if (empty($discountSetting)) {
            return $this->sendError('Discount Setting not found');
        }

        return $this->sendResponse($discountSetting->toArray(), 'Discount setting retrieved successfully');
    }

    /**
     * Update the specified DiscountSetting in storage.
     * PUT/PATCH /discountSettings/{id}
     *
     * @param int $id
     * @param UpdateDiscountSettingAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDiscountSettingAPIRequest $request)
    {
        $input = $request->all();

        /** @var DiscountSetting $discountSetting */
        $discountSetting = $this->discountSettingRepository->find($id);

        if (empty($discountSetting)) {
            return $this->sendError('Discount Setting not found');
        }

        $discountSetting = $this->discountSettingRepository->update($input, $id);

        return $this->sendResponse($discountSetting->toArray(), 'Discount setting updated successfully');
    }

    /**
     * Remove the specified DiscountSetting from storage.
     * DELETE /discountSettings/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DiscountSetting $discountSetting */
        $discountSetting = $this->discountSettingRepository->find($id);

        if (empty($discountSetting)) {
            return $this->sendError('Discount Setting not found');
        }

        $discountSetting->delete();

        return $this->sendSuccess('Discount setting deleted successfully');
    }
}
