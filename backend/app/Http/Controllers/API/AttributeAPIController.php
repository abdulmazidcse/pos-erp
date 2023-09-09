<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttributeAPIRequest;
use App\Http\Requests\API\UpdateAttributeAPIRequest;
use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class AttributeController
 * @package App\Http\Controllers\API
 */

class AttributeAPIController extends AppBaseController
{
    /** @var  AttributeRepository */
    private $attributeRepository;

    public function __construct(AttributeRepository $attributeRepo)
    {
        $this->attributeRepository = $attributeRepo;
    }

    /**
     * Display a listing of the Attribute.
     * GET|HEAD /attributes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $attributes = $this->attributeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($attributes->toArray(), 'Attributes retrieved successfully');
    }

    /**
     * Store a newly created Attribute in storage.
     * POST /attributes
     *
     * @param CreateAttributeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAttributeAPIRequest $request)
    {
        $input = $request->all();

        $attribute = $this->attributeRepository->create($input);

        return $this->sendResponse($attribute->toArray(), 'Attribute saved successfully');
    }

    /**
     * Display the specified Attribute.
     * GET|HEAD /attributes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->find($id);

        if (empty($attribute)) {
            return $this->sendError('Attribute not found');
        }

        return $this->sendResponse($attribute->toArray(), 'Attribute retrieved successfully');
    }

    /**
     * Update the specified Attribute in storage.
     * PUT/PATCH /attributes/{id}
     *
     * @param int $id
     * @param UpdateAttributeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAttributeAPIRequest $request)
    {
        $input = $request->all();

        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->find($id);

        if (empty($attribute)) {
            return $this->sendError('Attribute not found');
        }

        $attribute = $this->attributeRepository->update($input, $id);

        return $this->sendResponse($attribute->toArray(), 'Attribute updated successfully');
    }

    /**
     * Remove the specified Attribute from storage.
     * DELETE /attributes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->find($id);

        if (empty($attribute)) {
            return $this->sendError('Attribute not found');
        }

        $attribute->delete();

        return $this->sendSuccess('Attribute deleted successfully');
    }
}
