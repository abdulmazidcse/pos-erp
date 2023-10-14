<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthAPIController extends AppBaseController
{
    // User Login
    public function login(Request $request){  
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $query  = User::query(); 
        $query->where(function ($query) use ($email) {
            $query->where('email', $email );
            $query->orWhere('user_code', $email ); 
        });
        $user = $query->first();
        $inputs = $request->all();
        $inputs['email'] = $user ? $user->email : '';
        $credentials = [
            'email' => $inputs['email'],
            'password' => $inputs['password']
        ]; 
        if((!$user || !Hash::check($request->password, $user->password)) || !Auth::attempt($credentials)) { 
            return response()->json([
                'success'   => false,
                'message'   => 'Login Failed',
                'error'     => 'Invalid Credentials'
            ], 401);
        }

        $userToken    = $user->createToken('authToken');
        $accessToken    = $userToken->accessToken;
        $token          = $userToken->token; 
        $user_all_permissions = $user->getAllPermissions();

        $user_role_status = $user->roles->map(function ($item) {
            if($item->slug == "shop-manager" || $item->slug == "sales-man") {
                return true;
            }
            return false;
        }); 

        if(!empty($user_role_status)) {
            $user_outlet_id = 0;
            if(sizeof($user->outlets) > 0) {
                $user_outlet_id = $user->outlets[0]->id;
            }
            $user->update(['outlet_id' => $user_outlet_id]);
        }

        $return_data    = [ 
            'user' => new UserResource(auth()->user()),
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(), 
            'user_all_permissions'  => $user_all_permissions,
            'data_check'    => 1
        ]; 
        return $this->sendResponse($return_data, 'You are successfully logged in'); 
    } 

    public function unlockDiscount(Request $request){ 
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]); 
        // $user   = User::where('email', $request->email)->first();
        $email = $request->input('email');        
        $query  = User::query(); 
        $query->where(function ($query) use ($email) {
            $query->where('email', $email );
            $query->orWhere('user_code', $email ); 
        });
        $user = $query->first();
        if(!$user || !Hash::check($request->password, $user->password)) { 
            return response()->json([
                'success'   => false,
                'message'   => 'Invalid Credentials',
                'error'     => 'Invalid Credentials'
            ], 401);
        } 
        $allowDiscount = false;
        $user_role_status = $user->roles->map(function ($item) use ($allowDiscount) {
            if(($item->slug == "shop-manager") || ($item->slug == "admin") || ($item->slug == "super-admin")) {
                return true;
            } 
        });  
        if((sizeof($user_role_status) > 0 ) && $user_role_status[0]) { 
            if($user_role_status[0]){
                $allowDiscount = true; 
            } 
        } 
        $return_data    = [ 
            'status' => true,
            'discount' => $allowDiscount,
            'message' => 'You are successfully logged in', 
        ]; 
        return $this->sendResponse($return_data, 'You are successfully logged in'); 
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
