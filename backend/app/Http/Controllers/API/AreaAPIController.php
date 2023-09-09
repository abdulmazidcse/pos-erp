<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAreaAPIRequest;
use App\Http\Requests\API\UpdateAreaAPIRequest;
use App\Models\Area;
use App\Repositories\AreaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class AreaController
 * @package App\Http\Controllers\API
 */

class AreaAPIController extends AppBaseController
{
    /** @var  AreaRepository */
    private $areaRepository;

    public function __construct(AreaRepository $areaRepo)
    {
        $this->areaRepository = $areaRepo;
    }

    /**
     * Display a listing of the Area.
     * GET|HEAD /areas
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

        $query = Area::with(['districts' => function($query){
            $query->select('id', 'name');
        }])->select('id', 'name', 'bn_name', 'district_id', 'status', 'created_at')->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('bn_name', 'like', '%' .$searchValue. '%');
            });
        }

        $areas = $query->paginate($length);
        $return_data    = [
            'data' => $areas,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Areas retrieved successfully');
    }

    public function index(Request $request)
    {
        $areas = $this->areaRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($areas->toArray(), 'Areas retrieved successfully');
    }

    /**
     * Store a newly created Area in storage.
     * POST /areas
     *
     * @param CreateAreaAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAreaAPIRequest $request)
    {
        $this->validate($request, [
            'name'   => 'required',
            'bn_name'    => 'required',
            'district_id'    => 'required',
        ]);

        $input = $request->all();

        $area = $this->areaRepository->create($input);

        return $this->sendResponse($area->toArray(), 'Area saved successfully');
    }

    /**
     * Display the specified Area.
     * GET|HEAD /areas/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Area $area */
        $area = $this->areaRepository->find($id);

        if (empty($area)) {
            return $this->sendError('Area not found');
        }

        return $this->sendResponse($area->toArray(), 'Area retrieved successfully');
    }

    /**
     * Update the specified Area in storage.
     * PUT/PATCH /areas/{id}
     *
     * @param int $id
     * @param UpdateAreaAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAreaAPIRequest $request)
    {
        $this->validate($request, [
            'name'   => 'required',
            'bn_name'    => 'required',
            'district_id'    => 'required',
        ]);

        $input = $request->all();

        /** @var Area $area */
        $area = $this->areaRepository->find($id);

        if (empty($area)) {
            return $this->sendError('Area not found');
        }

        $area = $this->areaRepository->update($input, $id);

        return $this->sendResponse($area->toArray(), 'Area updated successfully');
    }

    /**
     * Remove the specified Area from storage.
     * DELETE /areas/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Area $area */
        $area = $this->areaRepository->find($id);

        if (empty($area)) {
            return $this->sendError('Area not found');
        }

        $area->delete();

        return $this->sendSuccess('Area deleted successfully');
    }
}
