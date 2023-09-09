<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCostCenterAPIRequest;
use App\Http\Requests\API\UpdateCostCenterAPIRequest;
use App\Models\CostCenter;
use App\Repositories\CostCenterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class CostCenterController
 * @package App\Http\Controllers\API
 */

class CostCenterAPIController extends AppBaseController
{
    /** @var  CostCenterRepository */
    private $costCenterRepository;

    public function __construct(CostCenterRepository $costCenterRepo)
    {
        $this->costCenterRepository = $costCenterRepo;
    }

    /**
     * Display a listing of the CostCenter.
     * GET|HEAD /costCenters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $costCenters = $this->costCenterRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($costCenters->toArray(), 'Cost Centers retrieved successfully');
    }


    public function getCostCenterList(Request $request)
    {
        $columns = ['center_name', 'companies.name', 'outlets.name', 'note', 'status'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = CostCenter::with(['companies', 'outlets'])->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('center_name', 'like', '%' .$searchValue. '%');
//                $query->orWhere('name', 'like', '%' .$searchValue. '%');
//                $query->orWhere('numbering', 'like', '%' .$searchValue. '%');
            });
        }

        $entry_types = $query->paginate($length);
        $return_data    = [
            'data' => $entry_types,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Cost Centers retrieved successfully');
    }

    /**
     * Store a newly created CostCenter in storage.
     * POST /costCenters
     *
     * @param CreateCostCenterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCostCenterAPIRequest $request)
    {
        $this->validate($request, [
            'company_id'  => 'required',
            'center_name'   => 'required'
        ]);

        $company_id = $request->get('company_id');
        $outlet_id  = $request->get('outlet_id') ?? 0;
        $center_name = $request->get('center_name');

        $uniqueExists   = CostCenter::where('company_id', $company_id)
                                        ->where('outlet_id', $outlet_id)
                                        ->where('center_name', $center_name)
                                        ->get();

        if(count($uniqueExists) > 0) {
            $errors = [
                'center_name' => ['This Center Name Already Exists!']
            ];
            return response()->json([
                'success' => false,
                'message' => 'This given data invalid',
                'errors' => $errors
            ], 422);
        }

        $input = $request->all();
        $input['outlet_id'] = $outlet_id;

        $costCenter = $this->costCenterRepository->create($input);

        return $this->sendResponse($costCenter->toArray(), 'Cost Center saved successfully');
    }

    /**
     * Display the specified CostCenter.
     * GET|HEAD /costCenters/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CostCenter $costCenter */
        $costCenter = $this->costCenterRepository->find($id);

        if (empty($costCenter)) {
            return $this->sendError('Cost Center not found');
        }

        return $this->sendResponse($costCenter->toArray(), 'Cost Center retrieved successfully');
    }

    /**
     * Update the specified CostCenter in storage.
     * PUT/PATCH /costCenters/{id}
     *
     * @param int $id
     * @param UpdateCostCenterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCostCenterAPIRequest $request)
    {
        $this->validate($request, [
            'company_id'  => 'required',
            'center_name'   => 'required'
        ]);

        $company_id = $request->get('company_id');
        $outlet_id  = $request->get('outlet_id') ?? 0;
        $center_name = $request->get('center_name');

        $uniqueExists   = CostCenter::where('company_id', $company_id)
                                        ->where('outlet_id', $outlet_id)
                                        ->where('center_name', $center_name)
                                        ->where('id', '!=', $id)
                                        ->get();

        if(count($uniqueExists) > 0) {
            $errors = [
                'center_name' => ['This Center Name Already Exists!']
            ];
            return response()->json([
                'success' => false,
                'message' => 'This given data invalid',
                'errors' => $errors
            ], 422);
        }
        $input = $request->all();
        $input['outlet_id'] = $outlet_id;

        /** @var CostCenter $costCenter */
        $costCenter = $this->costCenterRepository->find($id);

        if (empty($costCenter)) {
            return $this->sendError('Cost Center not found');
        }

        $costCenter = $this->costCenterRepository->update($input, $id);

        return $this->sendResponse($costCenter->toArray(), 'CostCenter updated successfully');
    }

    /**
     * Remove the specified CostCenter from storage.
     * DELETE /costCenters/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CostCenter $costCenter */
        $costCenter = $this->costCenterRepository->find($id);

        if (empty($costCenter)) {
            return $this->sendError('Cost Center not found');
        }

        $costCenter->delete();

        return $this->sendSuccess('Cost Center deleted successfully');
    }
}
