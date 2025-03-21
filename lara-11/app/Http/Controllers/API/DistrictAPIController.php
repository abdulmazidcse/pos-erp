<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDistrictAPIRequest;
use App\Http\Requests\API\UpdateDistrictAPIRequest;
use App\Models\District;
use App\Repositories\DistrictRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class DistrictController
 * @package App\Http\Controllers\API
 */

class DistrictAPIController extends AppBaseController
{
    /** @var  DistrictRepository */
    private $districtRepository;

    public function __construct(DistrictRepository $districtRepo)
    {
        $this->districtRepository = $districtRepo;
    }

    /**
     * Display a listing of the District.
     * GET|HEAD /districts
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $columns = ['name', 'bn_name', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = District::select('id', 'name', 'bn_name', 'division_id', 'status', 'created_at')->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
               $query->where('name', 'like', '%' .$searchValue. '%');
               $query->orWhere('bn_name', 'like', '%' .$searchValue. '%');
            });
        }

        $districts = $query->paginate($length);
        $return_data    = [
            'data' => $districts,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Districts retrieved successfully');
    }

    public function index(Request $request)
    {
        // $districts = $this->districtRepository->active()->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );
        $districts = $this->districtRepository->allQuery()->active()->get();

        return $this->sendResponse($districts->toArray(), 'Districts retrieved successfully');
    }

    /**
     * Store a newly created District in storage.
     * POST /districts
     *
     * @param CreateDistrictAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDistrictAPIRequest $request)
    {
        $this->validate($request, [
           'name'   => 'required',
           'bn_name'    => 'required',
           'division_id'    => 'required',
        ]);

        $input = $request->all();

        $district = $this->districtRepository->create($input);

        return $this->sendResponse($district->toArray(), 'District saved successfully');
    }

    /**
     * Display the specified District.
     * GET|HEAD /districts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var District $district */
        $district = $this->districtRepository->find($id);

        if (empty($district)) {
            return $this->sendError('District not found');
        }

        return $this->sendResponse($district->toArray(), 'District retrieved successfully');
    }

    /**
     * Update the specified District in storage.
     * PUT/PATCH /districts/{id}
     *
     * @param int $id
     * @param UpdateDistrictAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDistrictAPIRequest $request)
    {
        $this->validate($request, [
            'name'   => 'required',
            'bn_name'    => 'required',
            'division_id'    => 'required',
        ]);

        $input = $request->all();

        /** @var District $district */
        $district = $this->districtRepository->find($id);

        if (empty($district)) {
            return $this->sendError('District not found');
        }

        $district = $this->districtRepository->update($input, $id);

        return $this->sendResponse($district->toArray(), 'District updated successfully');
    }

    /**
     * Remove the specified District from storage.
     * DELETE /districts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function districtAreas($id){
        $district = District::with('areas')->find($id);
        return $this->sendResponse($district->toArray(), 'District updated successfully');
    }
    public function destroy($id)
    {
        /** @var District $district */
        $district = $this->districtRepository->find($id);

        if (empty($district)) {
            return $this->sendError('District not found');
        }

        $district->delete();

        return $this->sendSuccess('District deleted successfully');
    }
}
