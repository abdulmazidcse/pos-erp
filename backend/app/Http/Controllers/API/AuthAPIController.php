<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\Outlet;
use App\Models\UsersOutlet;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\VerifiesEmails; 
use Illuminate\Support\Facades\DB;
use Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Events\UserRegistered;
use App\Mail\ResetPasswordOtp;
use App\Mail\VerifyOtp;
use App\Services\PHPMailerService;
class AuthAPIController extends AppBaseController
{ 
    use VerifiesEmails;

    public function testMail(){

        // $mailer = new PHPMailerService();
        // $user = User::find('58');
        // $to = $user->email;
        // $subject = 'Test Email Subject';
        // $body = '<p>This is a test email sent using PHPMailer in Laravel!</p>';

        // $result = $mailer->sendEmail($to, $subject, $body);

        // return $result ? 'Email sent successfully!' : 'Failed to send email.';

        
        $user = User::find('58');
        event(new UserRegistered($user)); 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_code' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->user_code = $request->user_code;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->company_id = 0; //$request->company_id;
            $user->outlet_id = 0; //$outlet->id;
            $user->status = 0;// In-Active
            $user->password = bcrypt($request->password);
            $user->save();

            // Verification OTP 
            $otp =  mt_rand(100000, 999999); //Str::random(6); // 6-character token        
            $expiryTime = Carbon::now()->addMinutes(120); // Set OTP expiry time (e.g., 10 minutes from now) 
            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $request->email],
                ['otp' => $otp, 'token' => $otp, 'created_at' => now(), 'expires_at' => $expiryTime]
            );
            Mail::to($user->email)->send(new VerifyOtp($user, $otp));
            //  event(new UserRegistered($user));
            // Mail::to($this->user->email)->send(new \App\Mail\WelcomeEmail($this->user));

            DB::commit();
            return $this->sendResponse($user->toArray(), 'User registered successfully. Please check your email for verification.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
    public function storeAsee(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_code' => 'required|string|unique:users', // Ensure unique user code
            'email' => 'required|string|email|unique:users', // Ensure unique email
            'phone' => 'required|string|unique:users', // Ensure unique phone number (optional)
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } 
        $comp = new Company();
        $comp->name = $request->name;
        $comp->address = 'Address'; 
        $comp->contact_person_name = $request->name;
        $comp->contact_person_number = $request->phone;
        $comp->save();  
        $request->company_id = $comp->id;
        $outlet = self::outlet($request);
        DB::beginTransaction();
        try{
            $user = new User();
            $user->name = $request->name;
            $user->user_code = $request->user_code;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->company_id = $request->company_id;
            $user->outlet_id = $outlet->id;
            $user->password = bcrypt($request->password);
            $user->save();  
            
            $role = Role::find(11); 
            $user->assignRole($role);
            UsersOutlet::create([
                'user_id' => $user->id,
                'outlet_id' => $user->outlet_id
            ]);            
            DB::commit();
            return $this->sendResponse($user->toArray(), 'User saved successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();
        $getOtp = PasswordReset::where('email', $request->email)->first();

        // Check if OTP matches
        if ($user && $getOtp->otp == $request->otp) {
            // OTP is valid, mark user as verified or perform the next steps 
            if($user->company_id == 0){
                $comp = new Company();
                $comp->name = $user->name;
                $comp->address = 'Address'; 
                $comp->contact_person_name = $user->name;
                $comp->contact_person_number = $user->phone;
                $comp->save();  
                $request->company_id = $comp->id;
                $request->phone = $user->phone;
                $request->name = $user->name;
                $outlet = self::outlet($request);
                
                $role = Role::find(11); 
                $user->assignRole($role);
                UsersOutlet::create([
                    'user_id' => $user->id,
                    'outlet_id' => $outlet->id
                ]);           
                $user->update(['outlet_id' => $outlet->id, 'company_id' => $comp->id, 'status' => 1]); 
                $getOtp->delete();  
                event(new UserRegistered($user));    
            } 

            return response()->json(['message' => 'OTP verified successfully!'], 200);
        }

        return response()->json(['message' => 'Invalid OTP.'], 400);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }

    public function outlet(Request $request){
        return Outlet::create([
            'company_id'    => $request->company_id,
            'name'          => $request->name .'-'.$request->company_id, 
            'contact_person_name' => $request->name,
            'outlet_number' => $request->phone,
            'district_id' => '47',
            'area_id' => '109',
            'police_station' => 'Dhaka',
            'road_no' => '100',
            'plot_no' => '310',
            'latitude' => '23.777628',
            'longitude' => '90.405449',
            'status'    => 1
        ]);
    }


    // User Login
    public function login(Request $request){  
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $query  = User::query(); 
        $query->where(function ($query) use ($email) {
            $query->where('status', 1 );
            $query->where('email', $email );
            $query->orWhere('user_code', $email ); 
        });
        $user = $query->first();
        $inputs = $request->all(); 

        $getUser = User::where(function($query) use ($email) {
            $query->where('email', $email)
                  ->orWhere('user_code', $email);
        })
        ->where('company_id', '!=', 0)
        ->first();

        $inputs['email'] = $user ? $user->email : '';
        $credentials = [
            'email' => $inputs['email'],
            'password' => $inputs['password']
        ]; 
        if((!$user || !Hash::check($request->password, $user->password)) || !Auth::attempt($credentials)) { 
            return response()->json([
                'success'   => false,
                'verified'  => $getUser ? true : false,
                'message'   => 'Login Failed',
                'error'     => 'Invalid Credentials'
            ], 401);
        }

        $userToken      = $user->createToken('authToken');
        $accessToken    = $userToken->accessToken;
        $token          = $userToken->token; 
        // $plainTextToken  = $userToken->plainTextToken(); 
        $user_all_permissions = $user->getAllPermissions();
        // dd($user_all_permissions->toArray());

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

    public function loginWithToken(Request $request){
        $token = $request->input('token'); 
                
        // Validate the token and retrieve the user
        $user = User::where('api_token', $token)->first();
        
        if ($user) {
            // Log in the user (you might need to set session or similar)
            Auth::login($user);
            return response()->json(['message' => 'Logged in successfully']);
        }

        return response()->json(['error' => 'Invalid token'], 401);
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
 
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function sendOTP(Request $request){
        // Validate the incoming request
        $request->validate([
            'email' => 'required',
        ]);
    
        // Find the user with the provided email address
        $user = User::where('email', $request->email)
            ->orWhere('user_code', $request->email)
            ->orWhere('phone', $request->email)
            ->first(); 
                
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Generate unique token (OTP)
        $otp = mt_rand(100000, 999999); //Str::random(6); // 6-character token
    
        // Set OTP expiry time (e.g., 10 minutes from now)
        $expiryTime = Carbon::now()->addMinutes(10);
    
        // Store OTP and expiry time in the database
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'token' => $otp, 'created_at' => now(), 'expires_at' => $expiryTime]
        );
        Mail::to($user->email)->send(new ResetPasswordOtp($user, $otp));
    
        // Return success response with OTP
        return response()->json(['message' => 'OTP sent successfully', 'otp' => $otp]);
    }

    public function forgotPasswordOldnotUsed(Request $request) {

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->orWhere('user_code', $request->email)
            ->orWhere('phone', $request->email)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $token = Str::random(60);
        
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'otp' => $token,
            'created_at' => now()
        ]);

        // Send email with password reset link containing $token

        return response()->json(['message' => 'Password reset link sent to your email']);
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'password' => 'required|confirmed|min:6',
        ]); 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $passwordReset = PasswordReset::where('otp', $request->otp)
            ->where('email', $request->email)
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::where('email', $passwordReset->email)
            ->orWhere('user_code', $passwordReset->email)
            ->orWhere('phone', $passwordReset->email)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete password reset record
        $passwordReset->delete();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
