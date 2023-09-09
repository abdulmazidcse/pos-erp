<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOutletAPIRequest;
use App\Http\Requests\API\UpdateOutletAPIRequest;
use App\Http\Resources\OutletResource;
use App\Models\Outlet;
use App\Repositories\OutletRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;
use Response;

/**
 * Class OutletController
 * @package App\Http\Controllers\API
 */

class OutletAPIController extends AppBaseController
{
    /** @var  OutletRepository */
    private $outletRepository;

    public function __construct(OutletRepository $outletRepo)
    {
        $this->outletRepository = $outletRepo;
    }

    /**
     * Display a listing of the Outlet.
     * GET|HEAD /outlets
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $outlets = $this->outletRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $return_data = OutletResource::collection($outlets);
        return $this->sendResponse($return_data, 'Outlets retrieved successfully');
    }

    /**
     * Store a newly created Outlet in storage.
     * POST /outlets
     *
     * @param CreateOutletAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateOutletAPIRequest $request)
    {
        $this->validate($request, [
            'company_id'    => 'required',
            'name'          => 'required|unique:outlets,name,NULL,id,company_id,'.$request->company_id, 
            'contact_person_name' => 'required',
            'outlet_number' => 'required',
            'district_id' => 'required',
            'area_id' => 'required',
            'police_station' => 'required',
            'road_no' => 'required',
            'plot_no' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ],
        [
            'name.required'   => 'The outlet name field is required',
            'company_id.required'   => 'The company field is required',
        ]);

        $input = $request->all(); 
        $outlet = $this->outletRepository->create($input); 
        return $this->sendResponse($outlet->toArray(), 'Outlet saved successfully');
    }

    /**
     * Display the specified Outlet.
     * GET|HEAD /outlets/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Outlet $outlet */
        $outlet = $this->outletRepository->find($id);

        if (empty($outlet)) {
            return $this->sendError('Outlet not found');
        }

        $return_data = new OutletResource($outlet);
        return $this->sendResponse($return_data, 'Outlet retrieved successfully');
    }

    /**
     * Update the specified Outlet in storage.
     * PUT/PATCH /outlets/{id}
     *
     * @param int $id
     * @param UpdateOutletAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOutletAPIRequest $request)
    {
        $this->validate($request, [
            'company_id'    => 'required',
            'name'          => 'unique:outlets,name,'.$id.',id,company_id,'.$request->company_id,
            'contact_person_name' => 'required',
            'outlet_number' => 'required',
            'district_id' => 'required',
            'police_station' => 'required',
            'road_no' => 'required',
            'plot_no' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ],
        [
            'name.required'   => 'The outlet name field is required',
            'company_id.required'   => 'The company field is required',
        ]);


        $input = $request->all();

        /** @var Outlet $outlet */
        $outlet = $this->outletRepository->find($id);

        if (empty($outlet)) {
            return $this->sendError('Outlet not found');
        }

        $oldOutletImage = $outlet->outlet_image;
        if($request->hasFile('outlet_image')){
            $file = $request->file('outlet_image');
            $fileName   = $this->uploadFile($file, 'outlet', 'outlet_');

            $input['outlet_image'] = $fileName;

            $this->removeFile($oldOutletImage, 'outlet');
        }

        $outlet = $this->outletRepository->update($input, $id);

        return $this->sendResponse($outlet->toArray(), 'Outlet updated successfully');
    }

    /**
     * Remove the specified Outlet from storage.
     * DELETE /outlets/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Outlet $outlet */
        $outlet = $this->outletRepository->find($id);

        if (empty($outlet)) {
            return $this->sendError('Outlet not found');
        }

        $outlet->delete();

        return $this->sendSuccess('Outlet deleted successfully');
    }
}
