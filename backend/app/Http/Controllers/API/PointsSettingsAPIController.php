<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePointsSettingsAPIRequest;
use App\Http\Requests\API\UpdatePointsSettingsAPIRequest;
use App\Models\PointsSettings;
use App\Repositories\PointsSettingsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class PointsSettingsController
 * @package App\Http\Controllers\API
 */

class PointsSettingsAPIController extends AppBaseController
{
    /** @var  PointsSettingsRepository */
    private $pointsSettingsRepository;

    public function __construct(PointsSettingsRepository $pointsSettingsRepo)
    {
        $this->pointsSettingsRepository = $pointsSettingsRepo;
    }

    /**
     * Display a listing of the PointsSettings.
     * GET|HEAD /pointsSettings
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pointsSettings = $this->pointsSettingsRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($pointsSettings->toArray(), 'Points Settings retrieved successfully');
    }

    /**
     * Store a newly created PointsSettings in storage.
     * POST /pointsSettings
     *
     * @param CreatePointsSettingsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePointsSettingsAPIRequest $request)
    {
        $input = $request->all();

        $pointsSettings = $this->pointsSettingsRepository->create($input);

        return $this->sendResponse($pointsSettings->toArray(), 'Points Settings saved successfully');
    }

    /**
     * Display the specified PointsSettings.
     * GET|HEAD /pointsSettings/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PointsSettings $pointsSettings */
        $pointsSettings = $this->pointsSettingsRepository->find($id);

        if (empty($pointsSettings)) {
            return $this->sendError('Points Settings not found');
        }

        return $this->sendResponse($pointsSettings->toArray(), 'Points Settings retrieved successfully');
    }

    /**
     * Update the specified PointsSettings in storage.
     * PUT/PATCH /pointsSettings/{id}
     *
     * @param int $id
     * @param UpdatePointsSettingsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePointsSettingsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PointsSettings $pointsSettings */
        $pointsSettings = $this->pointsSettingsRepository->find($id);

        if (empty($pointsSettings)) {
            return $this->sendError('Points Settings not found');
        }

        $pointsSettings = $this->pointsSettingsRepository->update($input, $id);

        return $this->sendResponse($pointsSettings->toArray(), 'Points Settings updated successfully');
    }

    /**
     * Remove the specified PointsSettings from storage.
     * DELETE /pointsSettings/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PointsSettings $pointsSettings */
        $pointsSettings = $this->pointsSettingsRepository->find($id);

        if (empty($pointsSettings)) {
            return $this->sendError('Points Settings not found');
        }

        $pointsSettings->delete();

        return $this->sendSuccess('Points Settings deleted successfully');
    }
}
