<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGeneralSettingAPIRequest;
use App\Http\Requests\API\UpdateGeneralSettingAPIRequest;
use App\Models\GeneralSetting;
use App\Repositories\GeneralSettingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class GeneralSettingController
 * @package App\Http\Controllers\API
 */

class GeneralSettingAPIController extends AppBaseController
{
    /** @var  GeneralSettingRepository */
    private $generalSettingRepository;

    public function __construct(GeneralSettingRepository $generalSettingRepo)
    {
        $this->generalSettingRepository = $generalSettingRepo;
    }

    /**
     * Display a listing of the GeneralSetting.
     * GET|HEAD /generalSettings
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $generalSettings = $this->generalSettingRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($generalSettings->toArray(), 'General Settings retrieved successfully');
    }

    /**
     * Store a newly created GeneralSetting in storage.
     * POST /generalSettings
     *
     * @param CreateGeneralSettingAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateGeneralSettingAPIRequest $request)
    {
        $input = $request->all();

        $generalSetting = $this->generalSettingRepository->create($input);

        return $this->sendResponse($generalSetting->toArray(), 'General Setting saved successfully');
    }

    /**
     * Display the specified GeneralSetting.
     * GET|HEAD /generalSettings/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GeneralSetting $generalSetting */
        $generalSetting = $this->generalSettingRepository->find($id);

        if (empty($generalSetting)) {
            return $this->sendError('General Setting not found');
        }

        return $this->sendResponse($generalSetting->toArray(), 'General Setting retrieved successfully');
    }

    /**
     * Update the specified GeneralSetting in storage.
     * PUT/PATCH /generalSettings/{id}
     *
     * @param int $id
     * @param UpdateGeneralSettingAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGeneralSettingAPIRequest $request)
    {
        $input = $request->all();

        /** @var GeneralSetting $generalSetting */
        $generalSetting = $this->generalSettingRepository->find($id);

        if (empty($generalSetting)) {
            return $this->sendError('General Setting not found');
        }

        $generalSetting = $this->generalSettingRepository->update($input, $id);

        return $this->sendResponse($generalSetting->toArray(), 'GeneralSetting updated successfully');
    }

    /**
     * Remove the specified GeneralSetting from storage.
     * DELETE /generalSettings/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var GeneralSetting $generalSetting */
        $generalSetting = $this->generalSettingRepository->find($id);

        if (empty($generalSetting)) {
            return $this->sendError('General Setting not found');
        }

        $generalSetting->delete();

        return $this->sendSuccess('General Setting deleted successfully');
    }
}
