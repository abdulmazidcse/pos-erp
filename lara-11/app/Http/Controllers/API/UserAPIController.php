<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use App\Http\Resources\UserResource;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Repositories\UserRepository;
use App\Http\Resources\UserDataResource;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;
use Auth ;
use Storage;
use Str;
/**
 * Class UserController
 * @package App\Http\Controllers\API
 */

class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     * GET|USER /users
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $role_data      = Role::all()->pluck('name', 'id');

        $outlets = Outlet::where('status', 1)->get();
        $outlet_by_company = [];
        foreach($outlets as $outlet) {
            $outlet_by_company[$outlet->company_id][] = $outlet;
        }

        $return_data    = [
            'users' => UserResource::collection($users),
            'roles' => $role_data,
            'company_outlet'    => $outlet_by_company
        ];

        return $this->sendResponse($return_data, 'Users retrieved successfully');
    }

    public function userList(Request $request){
        $columns = ['id','name', 'email', 'user_code', 'profile_image', 'company_name', 'role'];

        $length = $request->input('length') ? $request->input('length') : 10;
        $column = $request->input('column') ? $request->input('column') : '0';
        $dir = $request->input('dir') ? $request->input('dir') : 'desc';
        $searchValue = $request->input('search');

        $query = User::with(['roles' => function($q){
                    $q->select('id', 'name');
                }])->select('users.id','users.name', 'users.email', 'users.user_code', 'users.phone','users.company_id','users.profile_image', 'companies.name as company_name','users.status')
                ->leftJoin('companies','companies.id','=','users.company_id')
                ->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('users.name', 'like', '%' .$searchValue. '%');
                $query->orWhere('users.email', 'like', '%' .$searchValue. '%'); 
                $query->orWhere('users.user_code', 'like', '%' .$searchValue. '%'); 
                $query->orWhereHas('roles', function($w) use ($searchValue) {
                    $w->where('roles.name', 'like', '%' .$searchValue. '%');
                });
            });
        }

        $users = $query->paginate($length);   

        $return_data    = [ 
            'data' => $users,  
            'draw' => $request->input('draw') ? $request->input('draw') : 0
        ];

        return $this->sendResponse($return_data, 'Users retrieved successfully');
    }

    public function user(Request $request)
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    public function helperData(Request $request){
        $outlets = Outlet::active()->get();
        $outlet_by_company = []; 
        $role_data      = Role::all()->pluck('name', 'id');
        $return_data    = [ 
            'roles' => $role_data,
            'outlet'=> $outlets, 
            'companies'     => Company::active()->get(),
        ];
        return $this->sendResponse($return_data, 'Users retrieved successfully');        
    }
    /**
     * Store a newly created User in storage.
     * POST /Users
     *
     * @param CreateUserAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUserAPIRequest $request)
    {
        $request->validate([
            'name'   => 'required',
            'user_code' => 'required|unique:users,user_code',
            'email'  => 'required|email|unique:users,email',
            'phone'  => 'required|unique:users,phone',
            'company_id'    => 'required',
            'password'  => 'required|confirmed|min:5',
            'password_confirmation' => 'required|min:5'

        ]);
        $input = $request->except(['password', 'password_confirmation', 'roles']);
        $input['password'] = bcrypt($request->password);

        if($request->hasFile('profile_image')){
            $file = $request->file('profile_image');
            $fileExt    = $file->getClientOriginalExtension();
            $fileName   = $this->uploadFile($file, 'users', 'user_profile_');
            $input['profile_image'] = $fileName;
        }

        DB::beginTransaction();
        try{
            $user = $this->userRepository->create($input);
            if(!empty($request->roles)){
                $user->assignRole(explode(",", $request->roles));
            }
            DB::commit();
            return $this->sendResponse($user->toArray(), 'User saved successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Display the specified User.
     * GET|USER /users/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user_data  = new UserResource($user);

        return $this->sendResponse($user_data, 'User retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     * PUT/PATCH /users/{id}
     *
     * @param int $id
     * @param UpdateUserAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserAPIRequest $request)
    {
        $request->validate([
            'name'   => 'required',
            'email'  => 'required|email|unique:users,email,'.$id,
            'phone'  => 'required|unique:users,phone,'.$id,
            'company_id'    => 'required',
            'password'  => 'sometimes|confirmed|min:5',
            'password_confirmation' => 'sometimes|min:5'

        ]);
        $input = $request->except(['password', 'password_confirmation', 'roles']);
        if("" != $request->password) {
            $input['password'] = bcrypt($request->password);
        }

        /** @var Head $head */
        $user = $this->userRepository->find($id);

        $old_image = $user->profile_image;
        if($request->hasFile('profile_image')){
            $file = $request->file('profile_image');
            $fileExt    = $file->getClientOriginalExtension();
            $fileName   = $this->uploadFile($file, 'users', 'user_profile_');
            $input['profile_image'] = $fileName;

            $this->removeFile($old_image, 'users');
        }

        if (empty($user)) {
            return $this->sendError('User not found');
        }



        DB::beginTransaction();
        try{
            $user = $this->userRepository->update($input, $id);
            if(!empty($request->roles)){
                $user->syncRoles(explode(",", $request->roles));
            }
            DB::commit();
            return $this->sendResponse($user->toArray(), 'User updated successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Remove the specified Head from storage.
     * DELETE /users/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }


        $old_image = $user->profile_image;
        if($user->delete()) {
            ///$this->removeFile($old_image, 'users');
        }

        return $this->sendSuccess('User deleted successfully');
    }


    /*
     * Custom Function
     *
     */

    // User Search
    public function users_search($key)
    {
        $users = User::limit(20)
            ->where('name', 'like', '%'.$key.'%')->get();

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    // Get Auth User
    public function userInfo(Request $request){
        $user_data = Auth::user();
        return $this->sendResponse( $user_data, 'User data successfully');

    }

    public function authApiUser(Request $request){
        $user_data = Auth::guard('api')->user();
        $roles_data = $user_data->roles;

        $return_data    = [
            'user'  => $user_data,
            'user_roles'    => $roles_data
        ];

        return $this->sendResponse( $return_data, 'User data retrieve successfully');
    }

    // Get User Profile
    public function  user_profile($id ,Request $request){
        $user_data = User::where('id',$id)->first();
        $data = New UserDataResource($user_data );
        return $this->sendResponse( $data , 'User profile successfully');
    }

    // User Profile Change
    public function  change_profile_image(Request $request){

        $user_data = Auth::user();
        if($request['files_base']){
            // foreach ($request->files_base as $key => $value) {
            $image_64 = $request->files_base ;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(20) . '.' . $extension;
            Storage::disk('posts')->put($imageName, base64_decode($image));
            // $fileInput['post_id'] = $postData->id;
            // $fileInput['user_id'] = $user_data->id;
            // $fileInput['path'] = $imageName;
            // $filesController = $this->filesPathRepository->create($fileInput);
            //}

            User::where('id',$user_data->id)
                ->update(['pro_image' => $imageName]);


        }

        $user_data = User::where('id',$user_data->id)->first();
        $data = New UserDataResource($user_data );
        return $this->sendResponse( $data , 'User profile successfully');
    }


}
