<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerGroupAPIRequest;
use App\Http\Requests\API\UpdateCustomerGroupAPIRequest;
use App\Models\CustomerGroup;
use App\Repositories\CustomerGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class CustomerGroupController
 * @package App\Http\Controllers\API
 */

class CustomerGroupAPIController extends AppBaseController
{
    /** @var  CustomerGroupRepository */
    private $customerGroupRepository;

    public function __construct(CustomerGroupRepository $customerGroupRepo)
    {
        $this->customerGroupRepository = $customerGroupRepo;
    }

    /**
     * Display a listing of the CustomerGroup.
     * GET|HEAD /customerGroups
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $customerGroups = $this->customerGroupRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($customerGroups->toArray(), 'Customer Groups retrieved successfully');
    }

    /**
     * Store a newly created CustomerGroup in storage.
     * POST /customerGroups
     *
     * @param CreateCustomerGroupAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerGroupAPIRequest $request)
    {
        $input = $request->all();

        $customerGroup = $this->customerGroupRepository->create($input);

        return $this->sendResponse($customerGroup->toArray(), 'Customer Group saved successfully');
    }

    /**
     * Display the specified CustomerGroup.
     * GET|HEAD /customerGroups/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupRepository->find($id);

        if (empty($customerGroup)) {
            return $this->sendError('Customer Group not found');
        }

        return $this->sendResponse($customerGroup->toArray(), 'Customer Group retrieved successfully');
    }

    /**
     * Update the specified CustomerGroup in storage.
     * PUT/PATCH /customerGroups/{id}
     *
     * @param int $id
     * @param UpdateCustomerGroupAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupRepository->find($id);

        if (empty($customerGroup)) {
            return $this->sendError('Customer Group not found');
        }

        $customerGroup = $this->customerGroupRepository->update($input, $id);

        return $this->sendResponse($customerGroup->toArray(), 'CustomerGroup updated successfully');
    }

    /**
     * Remove the specified CustomerGroup from storage.
     * DELETE /customerGroups/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupRepository->find($id);

        if (empty($customerGroup)) {
            return $this->sendError('Customer Group not found');
        }

        $customerGroup->delete();

        return $this->sendSuccess('Customer Group deleted successfully');
    }
}
