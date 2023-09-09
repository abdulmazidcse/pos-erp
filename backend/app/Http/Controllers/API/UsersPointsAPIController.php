<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUsersPointsAPIRequest;
use App\Http\Requests\API\UpdateUsersPointsAPIRequest;
use App\Models\UsersPoints;
use App\Repositories\UsersPointsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class UsersPointsController
 * @package App\Http\Controllers\API
 */

class UsersPointsAPIController extends AppBaseController
{
    /** @var  UsersPointsRepository */
    private $usersPointsRepository;

    public function __construct(UsersPointsRepository $usersPointsRepo)
    {
        $this->usersPointsRepository = $usersPointsRepo;
    }

    /**
     * Display a listing of the UsersPoints.
     * GET|HEAD /usersPoints
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $usersPoints = $this->usersPointsRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($usersPoints->toArray(), 'Users Points retrieved successfully');
    }

    /**
     * Store a newly created UsersPoints in storage.
     * POST /usersPoints
     *
     * @param CreateUsersPointsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUsersPointsAPIRequest $request)
    {
        $input = $request->all();

        $usersPoints = $this->usersPointsRepository->create($input);

        return $this->sendResponse($usersPoints->toArray(), 'Users Points saved successfully');
    }

    /**
     * Display the specified UsersPoints.
     * GET|HEAD /usersPoints/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UsersPoints $usersPoints */
        $usersPoints = $this->usersPointsRepository->find($id);

        if (empty($usersPoints)) {
            return $this->sendError('Users Points not found');
        }

        return $this->sendResponse($usersPoints->toArray(), 'Users Points retrieved successfully');
    }

    /**
     * Update the specified UsersPoints in storage.
     * PUT/PATCH /usersPoints/{id}
     *
     * @param int $id
     * @param UpdateUsersPointsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUsersPointsAPIRequest $request)
    {
        $input = $request->all();

        /** @var UsersPoints $usersPoints */
        $usersPoints = $this->usersPointsRepository->find($id);

        if (empty($usersPoints)) {
            return $this->sendError('Users Points not found');
        }

        $usersPoints = $this->usersPointsRepository->update($input, $id);

        return $this->sendResponse($usersPoints->toArray(), 'UsersPoints updated successfully');
    }

    /**
     * Remove the specified UsersPoints from storage.
     * DELETE /usersPoints/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UsersPoints $usersPoints */
        $usersPoints = $this->usersPointsRepository->find($id);

        if (empty($usersPoints)) {
            return $this->sendError('Users Points not found');
        }

        $usersPoints->delete();

        return $this->sendSuccess('Users Points deleted successfully');
    }
}
