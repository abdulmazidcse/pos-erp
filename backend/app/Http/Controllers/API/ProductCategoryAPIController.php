<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductCategoryAPIRequest;
use App\Http\Requests\API\UpdateProductCategoryAPIRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Repositories\ProductCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;
use Response;
use Auth;

/**
 * Class ProductCategoryController
 * @package App\Http\Controllers\API
 */

class ProductCategoryAPIController extends AppBaseController
{
    /** @var  ProductCategoryRepository */
    private $productCategoryRepository;

    public function __construct(ProductCategoryRepository $productCategoryRepo)
    {
        $this->productCategoryRepository = $productCategoryRepo;
    }

    /**
     * Display a listing of the ProductCategory.
     * GET|HEAD /productCategories
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //        $productCategories = $this->productCategoryRepository->all(
        //            $request->except(['skip', 'limit']),
        //            $request->get('skip'),
        //            $request->get('limit')
        //        );

        $parent_id = $request->get('parent_id');

        if(isset($parent_id)) {
            $productCategories = ProductCategory::where('status', 1)
                ->where('parent_id', $parent_id)->orderBy('name', 'ASC')->get();

        }else {
            $productCategories = ProductCategory::where('status', 1)->orderBy('parent_id', 'ASC')->orderBy('id', 'ASC')->get();
        }
        $return_data    = ProductCategoryResource::collection($productCategories);

        return $this->sendResponse($return_data, 'Product Categories retrieved successfully');
    }

    public function categoryList(Request $request)
    {
        
        $columns = ['id', 'name', 'parent_id', 'image', 'discount', 'description'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = ProductCategory::with(['parents' => function($query){
            $query->select('id','parent_id', 'name');
        }])->select('id', 'name', 'parent_id', 'image', 'discount', 'description', 'status', 'created_at')->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
            });
        }

        $product_categories = $query->paginate($length);
        $return_data    = [
            'data' => $product_categories,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Product categories retrieved successfully');
    }

    /**
     * Store a newly created ProductCategory in storage.
     * POST /productCategories
     *
     * @param CreateProductCategoryAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductCategoryAPIRequest $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'is_featured'   => 'required',
            'image'     => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2000'
        ]); 

        

        $input = $request->all(); 

        // return Request::file('image');
        // return $request->file('image');

        if($request->hasFile('image')){

            $file   = $request->file('image');
            $file_name = $this->uploadFile($file, 'product_categories', 'pcat_');

            $input['image'] = $file_name;
        }
        if(!$input['company_id']){
            $input['company_id'] = Auth::user() ? Auth::user()->company_id : 1;
        }
        

        $productCategory = $this->productCategoryRepository->create($input);

        return $this->sendResponse($productCategory->toArray(), 'Product Category saved successfully');
    }

    /**
     * Display the specified ProductCategory.
     * GET|HEAD /productCategories/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProductCategory $productCategory */
        $productCategory = $this->productCategoryRepository->find($id);

        if (empty($productCategory)) {
            return $this->sendError('Product Category not found');
        }

        $return_data    = new ProductCategoryResource($productCategory);

        return $this->sendResponse($return_data, 'Product Category retrieved successfully');
    }

    /**
     * Update the specified ProductCategory in storage.
     * PUT/PATCH /productCategories/{id}
     *
     * @param int $id
     * @param UpdateProductCategoryAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductCategoryAPIRequest $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'image'      => 'sometimes|image|max:2000'
        ]);

        $input = $request->all();

        /** @var ProductCategory $productCategory */
        $productCategory = $this->productCategoryRepository->find($id);

        if (empty($productCategory)) {
            return $this->sendError('Product Category not found');
        }

        $old_image  = $productCategory->image;
        if($request->hasFile('image')){

            $file   = $request->file('image');
            $file_name = $this->uploadFile($file, 'product_categories', 'pcat_');

            $input['image'] = $file_name;

            $this->removeFile($old_image, 'product_categories');
        }

        $productCategory = $this->productCategoryRepository->update($input, $id);

        return $this->sendResponse($productCategory->toArray(), 'ProductCategory updated successfully');
    }

    /**
     * Remove the specified ProductCategory from storage.
     * DELETE /productCategories/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProductCategory $productCategory */
        $productCategory = $this->productCategoryRepository->find($id);

        if (empty($productCategory)) {
            return $this->sendError('Product Category not found');
        }

        $productCategory->delete();

        return $this->sendSuccess('Product Category deleted successfully');
    }

    public function getProductParentCategory()
    {
        $product_category = ProductCategory::where('status', 1)->get();

        if(empty($product_category)) {
            return $this->sendError('Category data not found!');
        }
        $parent_category = array();
        $sub_category = array();
        foreach ($product_category as $category) {
            if($category->parent_id == 0) {
                $parent_category[] = $category;
            }

            if($category->parent_id != 0){
                $sub_category[$category->parent_id][]   = $category;
            }

        }

        $return_data    = [
            'parent_category'   => $parent_category,
            'sub_category'      => $sub_category,
        ];

        return $this->sendResponse($return_data, 'Category Retrieve Successfully');
    }
}
