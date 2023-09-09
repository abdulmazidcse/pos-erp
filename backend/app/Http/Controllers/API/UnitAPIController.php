<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnitAPIRequest;
use App\Http\Requests\API\UpdateUnitAPIRequest;
use App\Http\Resources\UnitCollection;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Repositories\UnitRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;
use Response;

/**
 * Class UnitController
 * @package App\Http\Controllers\API
 */

class UnitAPIController extends AppBaseController
{
    /** @var  UnitRepository */
    private $unitRepository;

    public function __construct(UnitRepository $unitRepo)
    {
        $this->unitRepository = $unitRepo;
    }

    /**
     * Display a listing of the Unit.
     * GET|HEAD /units
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $columns = ['unit_code', 'unit_name', 'base_unit', 'operator', 'operation_value','status'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir'); 
        $searchValue = $request->input('search');

        $query =  $this->unitRepository->allQuery()->orderBy($columns[$column], $dir);  

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('unit_code', 'like', '%' .$searchValue. '%');
                $query->orWhere('unit_name', 'like', '%' .$searchValue. '%');
                $query->orWhere('base_unit', 'like', '%' .$searchValue. '%');
                $query->orWhere('operator', 'like', '%' .$searchValue. '%'); 
                $query->orWhere('operation_value', 'like', '%' .$searchValue. '%'); 
            });
        }
        // dd($query->toSql());
        $data = $query->paginate($length);  
        $results = new UnitCollection($data);
        return $results; 
    }

    /**
     * Store a newly created Unit in storage.
     * POST /units
     *
     * @param CreateUnitAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUnitAPIRequest $request)
    {
        $this->validate($request, [
            'unit_code'     => 'required',
            'unit_name'     => 'required'
        ]);

        $input = $request->all();

        $unit = $this->unitRepository->create($input);

        return $this->sendResponse($unit->toArray(), 'Unit saved successfully');
    }

    /**
     * Display the specified Unit.
     * GET|HEAD /units/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->find($id);

        if (empty($unit)) {
            return $this->sendError('Unit not found');
        }

        $unit_data  = new UnitResource($unit);

        return $this->sendResponse($unit_data, 'Unit retrieved successfully');
    }

    /**
     * Update the specified Unit in storage.
     * PUT/PATCH /units/{id}
     *
     * @param int $id
     * @param UpdateUnitAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnitAPIRequest $request)
    {
        $this->validate($request, [
            'unit_code'     => 'required',
            'unit_name'     => 'required'
        ]);

        $input = $request->all();

        /** @var Unit $unit */
        $unit = $this->unitRepository->find($id);

        if (empty($unit)) {
            return $this->sendError('Unit not found');
        }

        $unit = $this->unitRepository->update($input, $id);

        return $this->sendResponse($unit->toArray(), 'Unit updated successfully');
    }

    /**
     * Remove the specified Unit from storage.
     * DELETE /units/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->find($id);

        if (empty($unit)) {
            return $this->sendError('Unit not found');
        }

        $unit->delete();

        return $this->sendSuccess('Unit deleted successfully');
    }
}
