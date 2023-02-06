<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ProfileInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Learn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Hash;
use App\Traits\FileUpload;
use App\Models\UserVerify;


class AuthController extends Controller
{
    use FileUpload;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register','submitForgetPasswordForm','submitResetPasswordForm','verifyAccount','resendVerificationEmail','showLearn']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response()->json(array(
            'success' => false,
            'error' => $validator->getMessageBag()),
            422);
        }


        if (! $token = auth('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = User::where('email',$request->email)->first();
        $email_verified_at = $user->email_verified_at;
        return $this->createNewToken($token,$email_verified_at);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'phone_number' => 'nullable|unique:users',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()){
            return response()->json(array(
            'success' => false,
            'error' => $validator->getMessageBag()),
            400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)],
                ));
        if($user){
            if($request->hasFile('profile_picture')){
                $profieImagePath = $this->FileUpload($request->profile_picture,'profile');
            }
            $profileInformation = new ProfileInformation;
            $profileInformation->user_id = $user->id;
            $profileInformation->profile_picture = isset($profieImagePath)?$profieImagePath:null;
            $profileInformation->save();
        }
        $user = User::with('profileInformation')->findOrFail($user->id);
        $email_verification_OTP = mt_rand(100000,999999);
        UserVerify::create([
            'user_id' => $user->id,
            'token' => $email_verification_OTP
            ]);
        Mail::send('email.EmailVerificationEmail', ['otp' => $email_verification_OTP], function($message) use($request){
                $message->to($request->email);
                $message->subject('Welcome to Recepient');
            });
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'otp'=>$email_verification_OTP
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth('api')->logout();
        return response()->json(['message' => 'User successfully signed out'],200);
    }
    public function verifyAccount($userId,$otp){
        $verifyUser = UserVerify::where('user_id',$userId)->where('token', $otp)->first();
        $user=User::where('id',$userId)->first();
        $message = 'Sorry your email cannot be identified.';

        if(!is_null($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->email_verified_at) {
                $verifyUser->user->email_verified_at = Carbon::now();
                $verifyUser->user->save();
                $message = "Your email have been verified successfully. Please Click here to login";
            } else {
                $message = "Your email has already verified.";
            }
            return response()->json(array('message' =>  $message,'code'=>'True','user'=>$user),200);
        }else{
            return response()->json(array('message' =>  $message,'code'=>'false','user'=>$user),200);

        }

    }

    public function resendVerificationEmail(Request $request){

        $user=User::where('email',$request->email)->first();
        $message="Email verfication mail has resent successfully";
        if($user && $user->email_verified_at==null){

            $verifyUser = UserVerify::where('user_id', $user->id)->first();
            Mail::send('emails.emailVerificationEmail', ['otp' => $verifyUser->otp], function($message) use($request){
                $message->to($request->email);
                $message->subject('Email Verification Mail');
            });
            return response()->json(array('message' => $message,'code'=>'True'),200);
        }
        else{
            return response()->json(array('message' => "User not registered",'code'=>'false'),200);
        }

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth('api')->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth('api')->user());
    }

    public function userList() {
        $users = User::latest()->get();
        return response()->json(["users"=>$users],200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token,$email_verified_at){
        return response()->json([
            'access_token' => $token,
            'email_verified_at'=>$email_verified_at,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
            'profile_information' => auth('api')->user()->profileInformation,
        ]);
    }


    public function submitForgetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()){
            return response()->json(array(
            'success' => false,
            'error' => $validator->getMessageBag()),
            422);
        }

        $OTP = Str::random(6);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $OTP,
            'created_at' => Carbon::now()
        ]);

        Mail::send('email.forgetPassword', ['token' => $OTP], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(["message" => "We have e-mailed you an OTP!"],200);
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
                            ->where([
                            'email' => $request->email,
                            'token' => $request->token
                            ])
                            ->first();

        if(!$updatePassword){
            return response()->json(['message'=>'Invalid token!'],404);
        }

        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email'=> $request->email])->delete();
        return response()->json(['message'=>'Your password has been changed!'],200);
    }
    public function editProfile(Request $request){

        $validator = Validator::make($request->all(), [
            'first_name'=>'nullable|string',
            'last_name'=>'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
            'phone_number' => 'nullable|unique:profile_information,phone_number'.Auth()->user()->profileInformation->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json(array(
            'success' => false,
            'error' => $validator->getMessageBag()),
            400);
        }
        $user = Auth()->user();
        if($request->input('old_password')){
            if (Hash::check($request->input('old_password'), Auth()->user()->password)){
                $user->password = bcrypt($request->input('password'));
            }else{
                return response()->json(['status'=>404, 'message'=>'Old Password not match']);
            }
        }
        $user->first_name = $request->first_name ? $request->first_name : $user->first_name;
        $user->last_name = $request->last_name ? $request->last_name : $user->last_name;
        $user->save();
        if($request->hasFile('profile_picture')){
            $profieImagePath = $this->FileUpload($request->profile_picture,'profile');
        }
        $profileInformation = ProfileInformation::where('user_id',Auth()->user()->id)->first();
        $profileInformation->phone_number = $request->phone_number??$profileInformation->phone_number;
        $profileInformation->profile_picture = isset($profieImagePath)?$profieImagePath:$profileInformation->profile_picture;
        $profileInformation->save();
        return response()->json([
            'status'=>200,
            'message'=>'Profile updated successfully',
            'user' => auth('api')->user(),
            'profile_information' => auth('api')->user()->profileInformation,
            'is_answered_question' =>auth('api')->user()->survey ? true : false
        ]);

    }
    public function showLearn(){
        $learn = Learn::all();
        return response()->json([
            'status'=>200,
            'learn' => $learn,
        ]);
    }

}
