<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSizeAPIRequest;
use App\Http\Requests\API\UpdateSizeAPIRequest;
use App\Models\Size;
use App\Repositories\SizeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class SizeController
 * @package App\Http\Controllers\API
 */

class SizeAPIController extends AppBaseController
{
    /** @var  SizeRepository */
    private $sizeRepository;

    public function __construct(SizeRepository $sizeRepo)
    {
        $this->sizeRepository = $sizeRepo;
    }

    /**
     * Display a listing of the Size.
     * GET|HEAD /sizes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $sizes = $this->sizeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($sizes->toArray(), 'Sizes retrieved successfully');
    }

    /**
     * Store a newly created Size in storage.
     * POST /sizes
     *
     * @param CreateSizeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSizeAPIRequest $request)
    {
        $input = $request->all();

        $size = $this->sizeRepository->create($input);

        return $this->sendResponse($size->toArray(), 'Size saved successfully');
    }

    /**
     * Display the specified Size.
     * GET|HEAD /sizes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Size $size */
        $size = $this->sizeRepository->find($id);

        if (empty($size)) {
            return $this->sendError('Size not found');
        }

        return $this->sendResponse($size->toArray(), 'Size retrieved successfully');
    }

    /**
     * Update the specified Size in storage.
     * PUT/PATCH /sizes/{id}
     *
     * @param int $id
     * @param UpdateSizeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSizeAPIRequest $request)
    {
        $input = $request->all();

        /** @var Size $size */
        $size = $this->sizeRepository->find($id);

        if (empty($size)) {
            return $this->sendError('Size not found');
        }

        $size = $this->sizeRepository->update($input, $id);

        return $this->sendResponse($size->toArray(), 'Size updated successfully');
    }

    /**
     * Remove the specified Size from storage.
     * DELETE /sizes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Size $size */
        $size = $this->sizeRepository->find($id);

        if (empty($size)) {
            return $this->sendError('Size not found');
        }

        $size->delete();

        return $this->sendSuccess('Size deleted successfully');
    }
}
