<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\masters\MstRoleModel;
use App\Models\masters\MstUserModel;
use App\Models\auth\TblLoginAttemptModel;
use App\Models\common\TblLogModel;
use App\Services\Mail\MailService;
use App\Services\Security\RSAService;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
use Validator;

class LoginController extends Controller{
    public function index(){
        return view('auth.login');
    }

    public function publicKey(){
        return response()->json([
            'public_key' => file_get_contents(
                storage_path('keys/public.pem')
            ),
            'csrf_token' => csrf_token()
        ]);
    }
    public function validateLogin(Request $request){
        try{
            $username = RSAService::decrypt( $request->username ); 
            $password = RSAService::decrypt( $request->password );

            /* 
            |-------------------------------------------------------------------------- 
            | Validate Input 
            |-------------------------------------------------------------------------- 
            */ 
            $validator = Validator::make( 
                [ 'username' => $username, 'password' => $password ], 
                [ 'username' => [ 'required', 'email' ], 'password' => [ 'required' ] ] 
            );

            if ($validator->fails()) { 
                return response()->json([
                    'csrf_token' => csrf_token(), 
                    'success' => 0, 
                    'message' => $validator->errors()->first() 
                ], 422); 
            }

            /* 
            |-------------------------------------------------------------------------- 
            | Check Failed Attempts (Last 1 Hour) 
            |-------------------------------------------------------------------------- 
            */ 
            $failedAttempts = TblLoginAttemptModel::where( 'username', $username ) 
                        ->where('is_success', 0) 
                        ->where( 'create_date', '>=', now()->subHour() ) 
                        ->count(); 

            if ($failedAttempts >= 3) { 
                return response()->json([
                    'csrf_token' => csrf_token(), 
                    'success' => 0, 
                    'message' => 'Account locked. Please try again after 1 hour.' 
                ], 423); 
            }

            /* 
            |--------------------------------------------------------------------------
            |Find User 
            |-------------------------------------------------------------------------- 
            */
            $user = MstUserModel::where('email', $username) 
                ->where('is_active', 1) 
                ->where('is_delete', 0) 
                ->first();
            
            if (!$user) {
                TblLoginAttemptModel::create([ 
                    'username' => $username, 
                    'ip_address' => $request->ip(), 
                    'is_success' => 0 
                ]);

                return response()->json([
                    'csrf_token' => csrf_token(),
                    'success' => 0,
                    'message' => 'Invalid email or password'
                ],401);
            }           
            /*
            |--------------------------------------------------------------------------
            | Verify Password
            |--------------------------------------------------------------------------
            */
            if (!Hash::check($password, $user->password)) {
        
                TblLoginAttemptModel::create([ 
                    'username' => $username, 
                    'ip_address' => $request->ip(), 
                    'is_success' => 0 
                ]);

                TblLogModel::create([
                    'module'       => 'AUTH',
                    'module_id'    => $user->mst_users_id,
                    'mst_users_id' => $user->mst_users_id,
                    'action'       => 'ACCOUNT_LOCKED',
                    'ip_address'   => $request->ip(),
                    'remarks'      => 'Account locked after 3 failed login attempts'
                ]);
                $remainingAttempts = max(0, 3 - ($failedAttempts + 1));

                return response()->json([
                    'csrf_token' => csrf_token(), 
                    'success' => 0, 
                    'message' => $remainingAttempts > 0 
                        ? "Invalid email or password. {$remainingAttempts} attempt(s) remaining." 
                        : "Account locked for 1 hour." 
                ], 401);
            }
        
            /*
            |--------------------------------------------------------------------------
            | Generate Login OTP
            |--------------------------------------------------------------------------
            */

            $otp = random_int(100000, 999999);
            $otp = 123456;
            $user->otp = Hash::make($otp);
            $user->otp_expiry = now()->addMinutes(5);
            $user->otp_attempts = 0;
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Send OTP Email
            |--------------------------------------------------------------------------
            */
            MailService::sendOTP($user,$otp);

            /* 
            |--------------------------------------------------------------------------             
            | Regenerate Session 
            |-------------------------------------------------------------------------- 
            */
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->regenerate();

            /* 
            |-------------------------------------------------------------------------- 
            |Store Session 
            -------------------------------------------------------------------------- 
            */
            $role = MstRoleModel::where('mst_roles_id',$user->mst_roles_id)->first();
            session([
                'pending_login'                 => true,
                'pending_user_id'               => $user->mst_users_id,
                'pending_device_fingerprint'    => $request->device_fingerprint,
                'username'                      => $user->username, 
                'email'                         => $user->email, 
                'profile_photo'                 => $user->profile_photo,
                'role_name'                     => $role->role_name             
            ]);

            /* 
            |-------------------------------------------------------------------------- 
            | Record Successful Login 
            |-------------------------------------------------------------------------- 
            */ 
            TblLoginAttemptModel::create([ 
                'username' => $username, 
                'ip_address' => $request->ip(), 
                'is_success' => 1 
            ]);

            return response()->json([
                'success' => 1,
                'message' => 'OTP sent successfully.',
                'redirect' => route('otp.index')
            ]);

        }catch(Exception $e){
            Log::error( 
                'Login Error : ' . $e->getMessage() 
            ); 
            return response()->json([
                'csrf_token' => csrf_token(), 
                'success' => 0, 
                'message' => 'Unable to process login request' 
            ], 500);
        }
    }
    public function logout(Request $request){
        try {
            /*
            |--------------------------------------------------------------------------
            | Pending Login (Before OTP Verification)
            |--------------------------------------------------------------------------
            */
            if (session()->has('pending_login')) {
                $user = MstUserModel::find(session('pending_user_id'));
                if ($user) {

                    /*
                    |--------------------------------------------------------------------------
                    | Clear OTP Information
                    |--------------------------------------------------------------------------
                    */
                    $user->otp = null;
                    $user->otp_expiry = null;
                    $user->otp_attempts = 0;
                    $user->otp_last_sent_at = null;
                    $user->otp_resend_count = 0;
                    $user->save();
                    TblLogModel::create([
                        'module'       => 'AUTH',
                        'module_id'    => $user->mst_users_id,
                        'mst_users_id' => $user->mst_users_id,
                        'action'       => 'OTP_LOGOUT',
                        'ip_address'   => $request->ip(),
                        'remarks'      => 'User logged out before OTP verification.'
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Logged In User
            |--------------------------------------------------------------------------
            */
            elseif (session()->has('user_id')) {
                $user = MstUserModel::find(session('user_id'));
                if ($user) {
                    TblLogModel::create([
                        'module'       => 'AUTH',
                        'module_id'    => $user->mst_users_id,
                        'mst_users_id' => $user->mst_users_id,
                        'action'       => 'LOGOUT',
                        'ip_address'   => $request->ip(),
                        'remarks'      => 'User logged out successfully.'
                    ]);
                    $user->session_id = null;
                    $user->device_fingerprint = null;
                    $user->last_ip_address = null;
                    $user->last_user_agent = null;
                    $user->save();
                }
            }
            /*
            |--------------------------------------------------------------------------
            | Remove Session Record
            |--------------------------------------------------------------------------
            */
            DB::table('sessions')
                ->where(
                    'id',
                    $request->session()->getId()
                )
                ->delete();
            /*
            |--------------------------------------------------------------------------
            | Destroy Session
            |--------------------------------------------------------------------------
            */
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'You have been logged out successfully.'
                );
        }catch (Exception $e) {
            Log::error('Logout Error : '.$e->getMessage());
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Unable to logout.'
                );
        }
    }
}
