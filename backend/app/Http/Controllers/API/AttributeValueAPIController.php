<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttributeValueAPIRequest;
use App\Http\Requests\API\UpdateAttributeValueAPIRequest;
use App\Models\AttributeValue;
use App\Repositories\AttributeValueRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class AttributeValueController
 * @package App\Http\Controllers\API
 */

class AttributeValueAPIController extends AppBaseController
{
    /** @var  AttributeValueRepository */
    private $attributeValueRepository;

    public function __construct(AttributeValueRepository $attributeValueRepo)
    {
        $this->attributeValueRepository = $attributeValueRepo;
    }

    /**
     * Display a listing of the AttributeValue.
     * GET|HEAD /attributeValues
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $attributeValues = $this->attributeValueRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($attributeValues->toArray(), 'Attribute Values retrieved successfully');
    }

    /**
     * Store a newly created AttributeValue in storage.
     * POST /attributeValues
     *
     * @param CreateAttributeValueAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAttributeValueAPIRequest $request)
    {
        $input = $request->all();

        $attributeValue = $this->attributeValueRepository->create($input);

        return $this->sendResponse($attributeValue->toArray(), 'Attribute Value saved successfully');
    }

    /**
     * Display the specified AttributeValue.
     * GET|HEAD /attributeValues/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->attributeValueRepository->find($id);

        if (empty($attributeValue)) {
            return $this->sendError('Attribute Value not found');
        }

        return $this->sendResponse($attributeValue->toArray(), 'Attribute Value retrieved successfully');
    }

    /**
     * Update the specified AttributeValue in storage.
     * PUT/PATCH /attributeValues/{id}
     *
     * @param int $id
     * @param UpdateAttributeValueAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAttributeValueAPIRequest $request)
    {
        $input = $request->all();

        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->attributeValueRepository->find($id);

        if (empty($attributeValue)) {
            return $this->sendError('Attribute Value not found');
        }

        $attributeValue = $this->attributeValueRepository->update($input, $id);

        return $this->sendResponse($attributeValue->toArray(), 'AttributeValue updated successfully');
    }

    /**
     * Remove the specified AttributeValue from storage.
     * DELETE /attributeValues/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->attributeValueRepository->find($id);

        if (empty($attributeValue)) {
            return $this->sendError('Attribute Value not found');
        }

        $attributeValue->delete();

        return $this->sendSuccess('Attribute Value deleted successfully');
    }
}
