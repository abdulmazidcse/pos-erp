<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBrandAPIRequest;
use App\Http\Requests\API\UpdateBrandAPIRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\BrandCollection;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;
use Response;

/**
 * Class BrandController
 * @package App\Http\Controllers\API
 */

class BrandAPIController extends AppBaseController
{
    /** @var  BrandRepository */
    private $brandRepository;

    public function __construct(BrandRepository $brandRepo)
    {
        $this->brandRepository = $brandRepo;
    }

    /**
     * Display a listing of the Brand.
     * GET|HEAD /brands
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    { 
        $columns = ['id', 'order', 'name', 'description', 'website', 'status'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $sortKey = $request->input('sortKey');
        $searchValue = $request->input('search');

        $query =  $this->brandRepository->allQuery()->orderBy($columns[$column], $dir); 

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('order', 'like', '%' .$searchValue. '%');
                $query->orWhere('description', 'like', '%' .$searchValue. '%');
                $query->orWhere('website', 'like', '%' .$searchValue. '%'); 
                $query->orWhere('status', 'like', '%' .$searchValue. '%');
            });
        }

        $data = $query->paginate($length);  
        $results = new BrandCollection($data);
        return $results;

        //return $this->sendResponse($brands_data, 'Brands retrieved successfully');
    }
    public function indexBack(Request $request)
    {
        $brands = $this->brandRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $brands_data    = BrandResource::collection($brands);

        return $this->sendResponse($brands_data, 'Brands retrieved successfully');
    }

    /**
     * Store a newly created Brand in storage.
     * POST /brands
     *
     * @param CreateBrandAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBrandAPIRequest $request)
    {
        $this->validate($request, [
            'name'  => 'required',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2000'
        ]);


        $input = $request->all();

        if ($request->hasFile('logo')) {
            $logoFile  = $request->file('logo');
            $fileName   = $this->uploadFile($logoFile, 'brand', 'brand_');

            $input['logo']  = $fileName;
        }

        $brand = $this->brandRepository->create($input);

        return $this->sendResponse($brand->toArray(), 'Brand saved successfully');
    }

    /**
     * Display the specified Brand.
     * GET|HEAD /brands/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Brand $brand */
        $brand = $this->brandRepository->find($id);

        if (empty($brand)) {
            return $this->sendError('Brand not found');
        }

        $brand_data = new BrandResource($brand);

        return $this->sendResponse($brand_data, 'Brand retrieved successfully');
    }

    /**
     * Update the specified Brand in storage.
     * PUT/PATCH /brands/{id}
     *
     * @param int $id
     * @param UpdateBrandAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBrandAPIRequest $request)
    {
        $this->validate($request, [
            'name'  => 'required',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2000'
        ]);

        $input = $request->all();

        /** @var Brand $brand */
        $brand = $this->brandRepository->find($id);

        if (empty($brand)) {
            return $this->sendError('Brand not found');
        }

        $oldLogo    = $brand->logo;
        if ($request->hasFile('logo')) {
            $logoFile  = $request->file('logo');
            $fileName   = $this->uploadFile($logoFile, 'brand', 'brand_');

            $input['logo']  = $fileName;

            $this->removeFile($oldLogo, 'brand');
        }

        $brand = $this->brandRepository->update($input, $id);

        return $this->sendResponse($brand->toArray(), 'Brand updated successfully');
    }

    /**
     * Remove the specified Brand from storage.
     * DELETE /brands/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Brand $brand */
        $brand = $this->brandRepository->find($id);

        if (empty($brand)) {
            return $this->sendError('Brand not found');
        }

        $brand->delete();

        return $this->sendSuccess('Brand deleted successfully');
    }
}