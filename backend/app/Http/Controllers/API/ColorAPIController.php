<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateColorAPIRequest;
use App\Http\Requests\API\UpdateColorAPIRequest;
use App\Http\Resources\ColorCollection;
use App\Models\Color;
use App\Repositories\ColorRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class ColorController
 * @package App\Http\Controllers\API
 */

class ColorAPIController extends AppBaseController
{
    /** @var  ColorRepository */
    private $colorRepository;

    public function __construct(ColorRepository $colorRepo)
    {
        $this->colorRepository = $colorRepo;
    }

    /**
     * Display a listing of the Color.
     * GET|HEAD /colors
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $columns = ['name', 'code', 'status'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir'); 
        $searchValue = $request->input('search');

        $query =  $this->colorRepository->allQuery()->orderBy($columns[$column], $dir);  

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('code', 'like', '%' .$searchValue. '%');
                $query->orWhere('status', 'like', '%' .$searchValue. '%');
            });
        }
        // dd($query->toSql());
        $data = $query->paginate($length);  
        $results = new ColorCollection($data);
        return $results; 
        // $colors = $this->colorRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        // return $this->sendResponse($colors->toArray(), 'Colors retrieved successfully');
    }

    /**
     * Store a newly created Color in storage.
     * POST /colors
     *
     * @param CreateColorAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateColorAPIRequest $request)
    {
        $input = $request->all();

        $color = $this->colorRepository->create($input);

        return $this->sendResponse($color->toArray(), 'Color saved successfully');
    }

    /**
     * Display the specified Color.
     * GET|HEAD /colors/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Color $color */
        $color = $this->colorRepository->find($id);

        if (empty($color)) {
            return $this->sendError('Color not found');
        }

        return $this->sendResponse($color->toArray(), 'Color retrieved successfully');
    }

    /**
     * Update the specified Color in storage.
     * PUT/PATCH /colors/{id}
     *
     * @param int $id
     * @param UpdateColorAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateColorAPIRequest $request)
    {
        $input = $request->all();

        /** @var Color $color */
        $color = $this->colorRepository->find($id);

        if (empty($color)) {
            return $this->sendError('Color not found');
        }

        $color = $this->colorRepository->update($input, $id);

        return $this->sendResponse($color->toArray(), 'Color updated successfully');
    }

    /**
     * Remove the specified Color from storage.
     * DELETE /colors/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Color $color */
        $color = $this->colorRepository->find($id);

        if (empty($color)) {
            return $this->sendError('Color not found');
        }

        $color->delete();

        return $this->sendSuccess('Color deleted successfully');
    }
}
