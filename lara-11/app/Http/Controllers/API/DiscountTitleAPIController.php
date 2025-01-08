<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDiscountTitleAPIRequest;
use App\Http\Requests\API\UpdateDiscountTitleAPIRequest;
use App\Models\DiscountTitle;
use App\Repositories\DiscountTitleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class DiscountTitleController
 * @package App\Http\Controllers\API
 */

class DiscountTitleAPIController extends AppBaseController
{
    /** @var  DiscountTitleRepository */
    private $discountTitleRepository;

    public function __construct(DiscountTitleRepository $discountTitleRepo)
    {
        $this->discountTitleRepository = $discountTitleRepo;
    }

    /**
     * Display a listing of the DiscountTitle.
     * GET|HEAD /discountTitles
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $discountTitles = $this->discountTitleRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($discountTitles->toArray(), 'Discount Titles retrieved successfully');
    }

    /**
     * Store a newly created DiscountTitle in storage.
     * POST /discountTitles
     *
     * @param CreateDiscountTitleAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDiscountTitleAPIRequest $request)
    {
        $input = $request->all();

        $discountTitle = $this->discountTitleRepository->create($input);

        return $this->sendResponse($discountTitle->toArray(), 'Discount Title saved successfully');
    }

    /**
     * Display the specified DiscountTitle.
     * GET|HEAD /discountTitles/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DiscountTitle $discountTitle */
        $discountTitle = $this->discountTitleRepository->find($id);

        if (empty($discountTitle)) {
            return $this->sendError('Discount Title not found');
        }

        return $this->sendResponse($discountTitle->toArray(), 'Discount Title retrieved successfully');
    }

    /**
     * Update the specified DiscountTitle in storage.
     * PUT/PATCH /discountTitles/{id}
     *
     * @param int $id
     * @param UpdateDiscountTitleAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDiscountTitleAPIRequest $request)
    {
        $input = $request->all();

        /** @var DiscountTitle $discountTitle */
        $discountTitle = $this->discountTitleRepository->find($id);

        if (empty($discountTitle)) {
            return $this->sendError('Discount Title not found');
        }

        $discountTitle = $this->discountTitleRepository->update($input, $id);

        return $this->sendResponse($discountTitle->toArray(), 'DiscountTitle updated successfully');
    }

    /**
     * Remove the specified DiscountTitle from storage.
     * DELETE /discountTitles/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DiscountTitle $discountTitle */
        $discountTitle = $this->discountTitleRepository->find($id);

        if (empty($discountTitle)) {
            return $this->sendError('Discount Title not found');
        }

        $discountTitle->delete();

        return $this->sendSuccess('Discount Title deleted successfully');
    }
}
