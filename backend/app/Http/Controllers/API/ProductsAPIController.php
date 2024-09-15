<?php

namespace App\Http\Controllers\API;
use App\Models\StockProduct;
use Illuminate\Support\Str;
use App\Http\Requests\API\CreateProductsAPIRequest;
use App\Http\Requests\API\UpdateProductsAPIRequest;
use App\Http\Resources\RequisitionProductResource;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductBarcodes;
use App\Models\Supplier;
use App\Models\ProductKeywords; 
use App\Models\ProductCategory; 
use App\Models\Brand;  
use App\Models\Unit; 
use App\Models\Color; 
use App\Models\Size; 
use App\Models\Taxes; 
use App\Models\HoldSale; 
use App\Repositories\ProductsRepository;
use App\Repositories\ProductBarcodesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Validator;
use Response;
use Image;
use File;

use App\Http\Resources\PosProductResource;
use App\Http\Resources\ProductResource;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use App\Imports\ProductsImport;
use Auth;

/**
 * Class ProductsController
 * @package App\Http\Controllers\API
 */

class ProductsAPIController extends AppBaseController
{
    /** @var  ProductsRepository */
    private $productsRepository;
    private $ProductBarcodesRepository;

    public function __construct(ProductsRepository $productsRepo, ProductBarcodesRepository $ProductBarcodes)
    {
        $this->productsRepository = $productsRepo;
        $this->ProductBarcodesRepository = $ProductBarcodes;
    }

    /**
     * Display a listing of the Products.
     * GET|HEAD /products
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = auth()->user(); 
        $outlet_id = $request->get('outlet_id') ?? null; 
        $query = $this->productsRepository->allQuery()->active();
        if ($user->hasRole('Super Admin')) {            
            $query->when($outlet_id, function ($q, $outlet_id) {  
                $q->where('outlet_id', $outlet_id);
            });
        } else { 
            $query->where('outlet_id',  $user->outlet_id);
        }  
        $products = $query->get();
        $return_data = ProductResource::collection($products);
        return $this->sendResponse($return_data, 'Products retrieved successfully');
        //return $this->sendResponse($products->toArray(), 'Products retrieved successfully'); 

    }

    public function list(Request $request)
    {
        $user = auth()->user();  
        $roles = $user->roles()->pluck('name')->toArray(); 

        $columns = ['id', 'product_name','product_native_name', 'product_code', 'category_id', 'sub_category_id', 'brand_id', 'company_id', 'barcode_symbology', 'min_order_qty', 'cost_price', 'depo_price', 'mrp_price', 'abp_price','abp_qty','tax_method', 'product_tax', 'alert_quantity', 'thumbnail', 'supplier_id', 'short_description', 'description', 'is_ecommerce', 'is_expirable', 'purchase_measuring_unit', 'sales_measuring_unit', 'convertion_rate', 'carton_size', 'carton_cpu', 'allow_checkout_when_out_of_stock', 'outlet_id', 'quantity', 'status','discount'];
        // name&category_id=3&sub_cat_id=35&brand_id=1
        $length = $request->input('length');
        $column = $request->input('column'); 
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $category_id = $request->input('category_id');
        $sub_cat_id = $request->input('sub_cat_id');
        $brand_id = $request->input('brand_id');

        if (in_array('Super Admin', $roles)) { 
            $outlet_id  = $request->input('outlet_id');
        }else{
            $outlet_id  = $request->input('outlet_id') ? $request->input('outlet_id') : $user->outlet_id;
        }   

        $query = Product::with(['category' => function($query){
            $query->select('id', 'name');
        }])->select('id', 'product_name', 'product_native_name', 'product_code', 'category_id', 'sub_category_id', 'brand_id', 'barcode_symbology', 'min_order_qty', 'cost_price', 'depo_price', 'mrp_price', 'abp_price','abp_qty', 'tax_method', 'product_tax', 'alert_quantity', 'thumbnail', 'short_description', 'description', 'purchase_measuring_unit', 'sales_measuring_unit', 'convertion_rate', 'carton_size', 'carton_cpu', 'quantity', 'status','discount')->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('product_name', 'like', '%' .$searchValue. '%');
                $query->orWhere('product_code', 'like', '%' .$searchValue. '%'); 
            });
        }
        if($category_id) {
            $query->where(function ($query) use ($category_id) {
                $query->where('category_id', $category_id ); 
            });
        }
        if($sub_cat_id) {
            $query->where(function ($query) use ($sub_cat_id) {
                $query->where('sub_category_id',$sub_cat_id); 
            });
        }
        if($brand_id) {
            $query->where(function ($query) use ($brand_id) {
                $query->where('brand_id', 'like',$brand_id); 
            });
        }
        if(isset($outlet_id)) {
            $query->where('outlet_id', $outlet_id);
        }

        $products = $query->paginate($length);
        $return_data    = [
            'data' => $products,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Products retrieved successfully');
    }

    public function productForPos(Request $request)
    { 
        $searchValue = $request->input('search');
        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : array(); 
        if (in_array('Super Admin', $roles)) { 
            $outlet_id  = $request->input('outlet_id');
        }else{
            $outlet_id  = $request->input('outlet_id') ? $request->input('outlet_id') : $user->outlet_id;
        }   
        $allow_checkout = 1;
        $query = Product::select('products.*','stock_products.in_stock_quantity',
            'stock_products.stock_quantity','stock_products.out_stock_quantity',
            'stock_products.expires_date', 'stock_products.id as product_stock_id', 'stock_products.outlet_id')
            ->with('category','sub_category')
            ->join('stock_products','products.id','=','stock_products.product_id')
            ->groupBy('products.id','stock_products.expires_date','stock_products.outlet_id')
            ->where('stock_products.outlet_id', '!=', 0)
            ->where(function ($query) use ($allow_checkout){
                $query->where(function($query) {
                    $query->where('stock_products.stock_quantity','>',0);
                    $query->orWhere('stock_products.stock_weight','>',0);
                });
                $query->orWhere(function ($query) use ($allow_checkout) {
                    // $query->where('stock_products.stock_quantity','=',0);
                    $query->Where('products.allow_checkout_when_out_of_stock','=',$allow_checkout);
                });
            });

            if($searchValue) {
                $query->where(function ($query) use ($searchValue) {
                    $query->where('product_name', 'like', '%' .$searchValue. '%');
                    $query->orWhere('product_code', 'like', '%' .$searchValue. '%'); 
                });
            }
            
            $query->where('products.outlet_id', $outlet_id);
              

            // $query->when($outlet_id, function ($q, $outlet_id) {  
            //     return $q->where('products.outlet_id', $outlet_id);
            // });
            
        // $query->when(((Auth::user()->outlet_id ) && (Auth::user()->outlet_id != '0')), function ($q) {
        //     return $q->where('products.outlet_id', Auth::user()->outlet_id);
        // }); 
        
        // dd($queries);

        // return $query->toSql(); 
        
        $products = $query->get();
        $return_data = PosProductResource::collection($products);
        return $this->sendResponse($return_data, 'Products retrieved successfully'); 
    }

    public function productForPosWithoutLogin(Request $request)
    {  
        $allow_checkout = 1; 
        $query = Product::select('products.*','products.product_type','stock_products.in_stock_quantity',
        'stock_products.stock_quantity','stock_products.out_stock_quantity',
        'stock_products.expires_date', 'stock_products.id as product_stock_id') 
        ->with('category','sub_category')
        ->rightJoin('stock_products','products.id','=','stock_products.product_id') 
        ->groupBy('products.id','stock_products.expires_date')
        ->where('stock_products.stock_quantity','>',0)
        ->orWhere(function ($query) use ($allow_checkout) {
            $query->where('stock_products.stock_quantity','>',0);
            $query->where('products.allow_checkout_when_out_of_stock','=',$allow_checkout);
        });

        // $query->when(((Auth::user()->outlet_id ) && (Auth::user()->outlet_id != '0')), function ($q) {
        //     return $q->where('stock_products.outlet_id', Auth::user()->outlet_id);
        // });
        // return $query->toSql();
    
        $products2 = $query->get();
        $return_data = PosProductResource::collection($products2);
        return $this->sendResponse($return_data, 'Products retrieved successfully'); 
    }

    public function productForHoldSales(Request $request)
    {  
        $hold_sale_id = $request->get('hold_id'); 
        $holdSale = HoldSale::where('id',$hold_sale_id)->first();
        $query = Product::select('products.*','products.product_type','hold_sale_items.product_stock_id', 'hold_sale_items.quantity as stock_quantity', 'stock_products.expires_date') 
            ->with('category','sub_category')
            ->when($hold_sale_id, function ($q, $hold_sale_id) {
                return $q->where('hold_sale_id', $hold_sale_id);
            })
            ->rightJoin('hold_sale_items','products.id','=','hold_sale_items.product_id') 
            ->rightJoin('stock_products','products.id','=','stock_products.product_id') 
            ->groupBy('hold_sale_items.id');  
        $products = $query->get();  
        $return_data = PosProductResource::collection($products);
        $return_data = array('info'=> $holdSale, 'items' => $return_data);
        return $this->sendResponse($return_data, 'Products retrieved successfully'); 
    }

    public function productForStoreRequisition(Request $request)
    {
        $products = $this->productsRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );
        $return_data = RequisitionProductResource::collection($products);
        return $this->sendResponse($return_data, 'Products retrieved successfully');
        //return $this->sendResponse($products->toArray(), 'Products retrieved successfully');

    }



    public function productForPurchaseReturn(Request $request)
    {
//        return Auth::user()->outlet_id;

        $allow_checkout = 1;
        $query = Product::select('products.*','stock_products.in_stock_quantity',
            'stock_products.stock_quantity','stock_products.out_stock_quantity',
            'stock_products.expires_date', 'stock_products.id as product_stock_id', 'stock_products.outlet_id')
            ->with('category','sub_category')
            ->join('stock_products','products.id','=','stock_products.product_id')
            ->groupBy('products.id','stock_products.expires_date','stock_products.outlet_id')
            ->where('stock_products.outlet_id', '!=', 0)
            ->where(function ($query) use ($allow_checkout){
                $query->where(function($query) {
                    $query->where('stock_products.stock_quantity','>',0);
                    $query->orWhere('stock_products.stock_weight','>',0);
                });
                $query->orWhere(function ($query) use ($allow_checkout) {
                    $query->where('stock_products.stock_quantity','>',0);
                    $query->where('products.allow_checkout_when_out_of_stock','=',$allow_checkout);
                });
            });
        $query->when(((Auth::user()->outlet_id ) && (Auth::user()->outlet_id != '0')), function ($q) {
            return $q->where('stock_products.outlet_id', Auth::user()->outlet_id);
        });

//        return $query->toSql();
        $products = $query->get();
        $return_data = PosProductResource::collection($products);
        return $this->sendResponse($return_data, 'Products retrieved successfully');
    }

    /**
     * Store a newly created Products in storage.
     * POST /products
     *
     * @param CreateProductsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProductsAPIRequest $request)
    {
        $customValidationMessage = [];
        if($request->get('user_define_barcode')){
            foreach($request->get('user_define_barcode') as $key => $val)
            {  
                if( ProductBarcodes::where('code',$val)->first()){
                    $customValidationMessage['user_define_barcode.unique'][] = 'This barcode '.$val. ' already taken';
                }
            } 
        }
        
        $this->validate($request, [
            'product_type'  => 'required',
            'product_name'  => 'required',
            'product_native_name'  => 'required',
            'product_code'  => 'required|unique:products,product_code', 
            'user_define_barcode' => 'unique:product_barcodes,code',
            'category_id'  => 'required',
            'sub_category_id'  => 'required',
            'cost_price'  => 'required',
            'mrp_price'  => 'required',
            'min_order_qty'  => 'required',
            'tax_method'  => 'required',
            'product_tax'  => 'required',
            'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2000'
        ], $customValidationMessage);  
         
        $input = $request->all();

//        return $this->sendResponse($input, 'test');

        $category = ProductCategory::find($input['category_id']);
        if(!empty($category)){
            $input['category_id'] = $category->id;
        }else{
            $category = new ProductCategory; 
            $category->name = $input['category_id'];
            $category->parent_id = 0;
            $category->description = '';
            $category->status = 1;
            $category->order = 0;  
            $category->is_featured = 0;   
            $category->discount = 0;   
            $category->save();
            $input['category_id'] = $category->id;

            $scategory = new ProductCategory; 
            $scategory->name = $input['sub_category_id'];
            $scategory->parent_id = $input['category_id'];
            $scategory->description = '';
            $scategory->status = 1;
            $scategory->order = 0;  
            $scategory->is_featured = 0;   
            $scategory->discount = 0;   
            $scategory->save();
            $input['sub_category_id'] = $scategory->id;
        }
        if($request->hasFile('thumbnail')) {  
            $image = $request->file('thumbnail');
            $imageName = 'thumbnail_'.$input['product_code'].'.'.$request->thumbnail->extension();  
            $destinationPath = public_path('/products/thumbnail/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 666, true);
            }
            $img = Image::make($image->getRealPath());
            $height = Image::make($image)->height(); 
            $width = Image::make($image)->width(); 
            $img->resize(350,350, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$imageName);  
            $input['thumbnail'] = $imageName;
        }  
        // $file =  $_FILES['attachments'];
        //  return $file;

        $product_images = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $key => $file) {
                $destinationPath = public_path('/products/images/');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 666, true);
                }
                $name = time() . '_' . rand(1000, 4000) . '.' . $file->getClientOriginalExtension();
                Image::make($file)->save($destinationPath .'/'. $name); 
                $product_images[] = new ProductImages([
                    'name'    => $name,
                ]);
            }
        }
        //return response()->json($request->user_define_barcode);
        $user_define_barcode = [];
        if ($request->user_define_barcode) {
            $barcodes = $request->user_define_barcode;  
            foreach ($barcodes as $key => $value) { 
                $user_define_barcode[] = new ProductBarcodes([
                    'code'    => $value,
                ]);
            } 
        }
        //return response()->json($user_define_barcode);
        
        $productstags = [];
        if ($request->product_tags) {
            $products_tags  = explode(',', $request->product_tags);
            foreach ($products_tags as $key => $value) {
                $productstags[] = new ProductKeywords([
                    'name'    => $value,
                ]); 
            } 
        }
        $supplier_ids = [];
        if ($request->supplier_id) {
            $suppliers  = explode(',', $request->supplier_id);
            foreach ($suppliers as $key => $value) {
                $supplier_ids[] = $value;
            } 
        }
        $productsizes = [];
        if ($request->size) {
            $product_colors  = explode(',', $request->size);
            foreach ($product_colors as $key => $value) {
                $productsizes[] = $value;
            } 
        }
        $productcolors = [];
        if ($request->color) {
            $product_colors  = explode(',', $request->color);
            foreach ($product_colors as $key => $value) {
                $productcolors[] =   $value;
            } 
        }
        //return response()->json($request->user_define_barcode);

        \DB::beginTransaction(); 
        $productStock[] = new StockProduct([
            'category_id' => $input['category_id'], 
            'outlet_id' => $input['outlet_id'],
            'in_stock_quantity' => 0, 
            'in_stock_weight' => 0, 
            'stock_quantity' => 0, 
            'stock_weight' => 0, 
            'out_stock_quantity' => 0, 
            'out_stock_weight' => 0
        ]); 

        try{ 
            $products = $this->productsRepository->create($input);
            $products->images()->saveMany($product_images);
            $products->barcodes()->saveMany($user_define_barcode);            
            $products->keywords()->saveMany($productstags);
            $products->suppliers()->attach($supplier_ids);
            $products->sizes()->attach($productsizes);
            $products->colors()->attach($productcolors);
            $products->stock_product()->saveMany($productStock);
            \DB::commit();
            return $this->sendResponse($products->toArray(), 'Products saved successfully');

        }catch(\Exception $e){
            \DB::rollback();
            return $this->sendError($e->getMessage());
        } 
    }

    /**
     * Display the specified Products.
     * GET|HEAD /products/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Products $products */
        $products = $this->productsRepository->find($id);

        if (empty($products)) {
            return $this->sendError('Products not found');
        }
        $return_data = new ProductResource($products);

        return $this->sendResponse($return_data, 'Products retrieved successfully');
    }

    /**
     * Update the specified Products in storage.
     * PUT/PATCH /products/{id}
     *
     * @param int $id
     * @param UpdateProductsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProductsAPIRequest $request)
    {
        $customValidationMessage = [];
        if($request->get('user_define_barcode')){
            foreach($request->get('user_define_barcode') as $key => $val)
            {  
                if( ProductBarcodes::where('code',$val)->whereNotIn('product_id', [$id])->first()){
                    $customValidationMessage['user_define_barcode.unique'][] = 'This barcode '.$val.' already taken';
                }
            } 
        }
        
        $this->validate($request, [
            'product_type'  => 'required',
            'product_name'  => 'required',
            'product_native_name'  => 'required',
            'product_code'  => 'required|unique:products,product_code,'.$id,   
            'category_id'  => 'required',
            'sub_category_id'  => 'required',
            'cost_price'  => 'required',
            'mrp_price'  => 'required',
            'min_order_qty'  => 'required',
            'tax_method'  => 'required',
            'product_tax'  => 'required',
            'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2000'
        ], $customValidationMessage);
        $product = $this->productsRepository->find($id);
        $input = $request->all(); 
        if($request->hasFile('thumbnail')) {  
            $image = $request->file('thumbnail');
            $imageName = 'thumbnail_'.$input['product_code'].'.'.$request->thumbnail->extension();  
            $destinationPath = public_path('/products/thumbnail/'); 
            $img = Image::make($image->getRealPath());
            $height = Image::make($image)->height(); 
            $width = Image::make($image)->width(); 
            $img->resize(350,350, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$imageName);              
            // if(File::exists($destinationPath.'/'.$product->thumbnail)){    
            //     File::delete($destinationPath.'/'.$product->thumbnail);
            // }
            $input['thumbnail'] = $imageName; 
        }  
        // $file =  $_FILES['attachments'];
        //  return $file;

        $product_images = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $key => $file) {
                $destinationPath = public_path('/products/images/');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 666, true);
                }
                $name = time() . '_' . rand(1000, 4000) . '.' . $file->getClientOriginalExtension();
                Image::make($file)->save($destinationPath .'/'. $name); 
                $product_images[] = new ProductImages([
                    'name'    => $name,
                ]);
            }
        }
        //return response()->json($product_images);
        $user_define_barcode = [];
        if ($request->user_define_barcode) { 
            $barcodes = $request->user_define_barcode;   
            foreach ($barcodes as $key => $value) {  
                $user_define_barcode[] = new ProductBarcodes([
                    'code'    => $value,
                ]);
            } 
        } 
        //return response()->json($user_define_barcode);
        $productstags = [];
        if ($request->product_tags) {
            $products_tags  = explode(',', $request->product_tags);
            foreach ($products_tags as $key => $value) {
                $productstags[] = new ProductKeywords([
                    'name'    => $value,
                ]); 
            } 
        }
        //return response()->json($productstags);
        $supplier_ids = [];
        if ($request->supplier_id) {
            $suppliers  = explode(',', $request->supplier_id);
            foreach ($suppliers as $key => $value) {
                $supplier_ids[] =  $value;
            } 
        }
        $productsizes = [];
        if ($request->size) {
            $product_colors  = explode(',', $request->size);
            foreach ($product_colors as $key => $value) {
                $productsizes[] =  $value; 
            } 
        }
        $productcolors = [];
        if ($request->color) { 
            $product_colors  = explode(',', $request->color);
            foreach ($product_colors as $key => $value) { 
                $productcolors[] = $value; 
            } 
        }
        //return response()->json($productcolors); 

        \DB::beginTransaction();

        try{ 
            $products = $this->productsRepository->update($input, $id); 
            $products->images()->delete(); 
            $products->images()->saveMany($product_images);
            if(sizeof($productstags) > 0){
                if(sizeof($products->barcodes) >0){
                   $products->keywords()->delete(); 
                }                
                $products->keywords()->saveMany($productstags);
            }             
            if(sizeof($user_define_barcode) > 0){
                if(sizeof($products->barcodes) >0){
                   $products->barcodes()->delete(); 
                }                
                $products->barcodes()->saveMany($user_define_barcode);
            } 
            if(sizeof($productsizes) >0){
                $products->sizes()->sync($productsizes);
            }
            if(sizeof($productcolors) >0){
                $products->colors()->sync($productcolors);
            }
            if(sizeof($supplier_ids) >0){
                $products->suppliers()->sync($supplier_ids);
            }
            \DB::commit();
            return $this->sendResponse($products->toArray(), 'Products saved successfully');

        }catch(\Exception $e){
            \DB::rollback();
            return $this->sendError($e->getMessage());
        } 
        if (empty($products)) {
            return $this->sendError('Products not found');
        } 
        return $this->sendResponse($products->toArray(), 'Products updated successfully');
    }

    public function productsImport(CreateProductsAPIRequest $request){

//        return $this->sendResponse([$request->all(), $request->file('file')], "Test File");

        if($request->hasFile('file')) {  
            $products = $this->productsRepository->select('product_code');
            $proCode = $products->map(function($item){
                return $item->product_code;
            });

            $array = Excel::toArray('', $request->file('file'));
            $input = array();  
            $cat = '';
            $subcat = '';
            $brand = '';
            $exists_code = '';
            $inserted = 0;
            $exists = 0; 
            for ($i=0; $i < sizeof($array[0]); $i++) {   
            if($array[0][$i][1]){              
                if($array[0][$i][4]){
                    $cat = ProductCategory::where('name',$array[0][$i][4])->first();
                    $subcat = ProductCategory::where('name',$array[0][$i][5])->first();      
                    $brand = Brand::where('name',$array[0][$i][6])->first();      
                } 
                if (!in_array($array[0][$i][3], $proCode->toArray())) {
                    $inserted++;
                    $input[] = array(
                        'product_type' => $array[0][$i][0],
                        'product_name' => $array[0][$i][1],
                        'product_native_name' => $array[0][$i][2],
                        'product_code' => $array[0][$i][3],
                        'category_id' =>  $cat->id ?? 0, 
                        'sub_category_id' => $subcat->id ?? 0, 
                        'brand_id'   => $brand->id ?? 0,
                        'cost_price' => $array[0][$i][7],
                        'depo_price' => $array[0][$i][8],
                        'mrp_price'  => $array[0][$i][9],
                        'product_tax'  => $array[0][$i][10] ?? 1,
                        'tax_method' => $array[0][$i][11] ?? 1,
                        'alert_quantity' => $array[0][$i][12],
                        'short_description' => $array[0][$i][13],
                        'description' => $array[0][$i][14],
                        'is_expirable' => $array[0][$i][15],
                        'purchase_measuring_unit' => $array[0][$i][16],
                        'sales_measuring_unit' => $array[0][$i][17],
                    ); 
                }else{
                    $exists++;
                    $exists_code .= $array[0][$i][3].',';
                }
                }
            }  

            //return response()->json($input);
            array_shift($input);  

            \DB::beginTransaction();

            try{ 
                $products = $this->productsRepository->insert($input); 
                $customMessage = array(
                    'inserted' => $inserted.' products inserted successfully',
                    'exists' => $exists .' products exists',
                    'code' => $exists_code);
                \DB::commit();
                return $this->sendResponse($customMessage, 'Products saved successfully! ');

            }catch(\Exception $e){
                \DB::rollback();
                return $this->sendError($e->getMessage());
            }  
        } 
    }

    public function masterDataUpload(Request $request){         
        if($request->hasFile('file')) {   
            $array = Excel::toArray('', $request->file('file'));
            $input = array();  
            $cat = '';
            $subcat = '';
            $brand = '';
            $exists_code = '';
            $inserted = 0;
            $exists = 0; 
            \DB::beginTransaction();

            try{ 
                for ($i=0; $i < sizeof($array[0]); $i++) {   
                    if($array[0][$i][1]){ 
                        $category = ProductCategory::firstOrCreate([
                            'code' => $array[0][$i][1],
                            'name' => $array[0][$i][2],                
                            'parent_id' => 0
                        ]); 
                        $subCategory = ProductCategory::firstOrCreate([
                            'code' => $array[0][$i][3],
                            'name' => $array[0][$i][4],                
                            'parent_id' => $category->id
                        ]);
                        $subSubCategory = ProductCategory::firstOrCreate([
                            'code' => $array[0][$i][5],
                            'name' => $array[0][$i][6],                
                            'parent_id' => $subCategory->id
                        ]);  
                        $brand = Brand::firstOrCreate([
                            'code' => $array[0][$i][7],
                            'name' => $array[0][$i][8],
                        ]); 
                        $products = Product::firstOrCreate([
                            'product_type' => $array[0][$i][9],
                            'product_code' => $array[0][$i][10],
                            'product_name' => $array[0][$i][11],
                            'product_native_name' => $array[0][$i][12],                    
                            'category_id' =>  $category->id, 
                            'sub_category_id' => $subCategory->id, 
                            'sub_sub_category_id' => $subSubCategory->id, 
                            'brand_id'   => $brand->id,
                            'cost_price' => $array[0][$i][13],
                            'depo_price' => $array[0][$i][14],
                            'mrp_price'  => $array[0][$i][15],
                            'product_tax' => $array[0][$i][16],
                            'tax_method' => $array[0][$i][17],
                            'alert_quantity' => $array[0][$i][18],
                            'short_description' => $array[0][$i][19],
                            'description' => $array[0][$i][20],
                            'is_expirable' => $array[0][$i][21]
                        ]); 
                    }  
                }
                \DB::commit();
                return $this->sendResponse($products, 'Products saved successfully! ');

            }catch(\Exception $e){
                \DB::rollback();
                return $this->sendError($e->getMessage());
            }  
        } 
    }

    /**
     * Remove the specified Products from storage.
     * DELETE /products/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Products $products */
        $products = $this->productsRepository->find($id);

        if (empty($products)) {
            return $this->sendError('Products not found');
        }

        $products->delete();

        return $this->sendSuccess('Products deleted successfully');
    }


    public function productsHelper(){
        $data = array(
            'categories' => ProductCategory::parent()->get()->toArray(), 
            'brands' => Brand::get()->toArray(), 
            'units' => Unit::get()->toArray(),
            'suppliers' => Supplier::get()->toArray(),  
            'colors' => Color::get()->toArray(), 
            'sizes' => Size::get()->toArray(), 
            'taxes' => Taxes::get()->toArray(), 
        );
        return $this->sendResponse($data, 'Data retrieved successfully');
    }


    public function sampleProductsUploadFormate(){
        //return Excel::download(new UsersExport, 'users.xlsx');
        // $path = storage_path('app/' . $filename);

        // // Download file with custom headers
        // return response()->download($path, $filename, [
        //     'Content-Type' => 'application/vnd.ms-excel',
        //     'Content-Disposition' => 'inline; filename="' . $filename . '"'
        // ]);
        return Excel::download('','users.xlsx');
    }
}
