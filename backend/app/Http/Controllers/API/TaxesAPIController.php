<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxesAPIRequest;
use App\Http\Requests\API\UpdateTaxesAPIRequest;
use App\Models\Taxes;
use App\Repositories\TaxesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response; 
/**
 * Class TaxesController
 * @package App\Http\Controllers\API
 */

class TaxesAPIController extends AppBaseController
{
    /** @var  TaxesRepository */
    private $taxesRepository;

    public function __construct(TaxesRepository $taxesRepo)
    {
        $this->taxesRepository = $taxesRepo;
    }

    /**
     * Display a listing of the Taxes.
     * GET|HEAD /taxes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $taxes = $this->taxesRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($taxes->toArray(), 'Taxes retrieved successfully');
    }

    /**
     * Store a newly created Taxes in storage.
     * POST /taxes
     *
     * @param CreateTaxesAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxesAPIRequest $request)
    { 
        $this->Validate($request, [
            'title'  => 'required|unique:taxes,title',
            'value'  => 'required|unique:taxes,value'
        ]);

        $input = $request->all();

        $taxes = $this->taxesRepository->create($input);  

        return $this->sendResponse($taxes->toArray(), 'Taxes saved successfully');
    }

    /**
     * Display the specified Taxes.
     * GET|HEAD /taxes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Taxes $taxes */
        $taxes = $this->taxesRepository->find($id);

        if (empty($taxes)) {
            return $this->sendError('Taxes not found');
        }

        return $this->sendResponse($taxes->toArray(), 'Taxes retrieved successfully');
    }

    /**
     * Update the specified Taxes in storage.
     * PUT/PATCH /taxes/{id}
     *
     * @param int $id
     * @param UpdateTaxesAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxesAPIRequest $request)
    {
        $input = $request->all();

        /** @var Taxes $taxes */
        $taxes = $this->taxesRepository->find($id);

        if (empty($taxes)) {
            return $this->sendError('Taxes not found');
        }

        $taxes = $this->taxesRepository->update($input, $id);

        return $this->sendResponse($taxes->toArray(), 'Taxes updated successfully');
    }

    /**
     * Remove the specified Taxes from storage.
     * DELETE /taxes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Taxes $taxes */
        $taxes = $this->taxesRepository->find($id);

        if (empty($taxes)) {
            return $this->sendError('Taxes not found');
        }

        $taxes->delete();

        return $this->sendSuccess('Taxes deleted successfully');
    }
}
