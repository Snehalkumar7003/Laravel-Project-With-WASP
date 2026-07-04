<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\common\TblLogModel;
use App\Models\masters\MstRoleModel;
use App\Models\masters\MstUserModel;
use App\Services\Mail\MailService;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
use Validator;

class OtpController extends Controller{
    
    public function index(){
        return view('verification.mfa-otp');
    }

    public function resend(Request $request){
        try {
            /*
            |--------------------------------------------------------------------------
            | Validate Pending Login Session
            |--------------------------------------------------------------------------
            */
            if (!session()->has('pending_login') ||!session()->has('pending_user_id')) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Your login session has expired. Please login again.'
                ], 401);
            }

            /*
            |--------------------------------------------------------------------------
            | Get User
            |--------------------------------------------------------------------------
            */
            $user = MstUserModel::find(
                session('pending_user_id')
            );
            if (!$user) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Invalid request.'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Reset Resend Counter Every Hour
            |--------------------------------------------------------------------------
            */
            if (empty($user->otp_last_sent_at) || now()->diffInHours($user->otp_last_sent_at) >= 1) {
                $user->otp_resend_count = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | Maximum 5 Resends Per Hour
            |--------------------------------------------------------------------------
            */
            if ($user->otp_resend_count >= 5) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Maximum OTP resend limit reached. Please login again.'
                ], 429);
            }        
            /*
            |--------------------------------------------------------------------------
            | Allow Resend Only After 300 Seconds
            |--------------------------------------------------------------------------
            */
            if (!empty($user->otp_last_sent_at)) {

                $lastSentAt = Carbon::parse($user->otp_last_sent_at);

                $nextResendTime = $lastSentAt->copy()->addSeconds(300);

                if (now()->lt($nextResendTime)) {

                    $remaining = now()->diffInSeconds($nextResendTime);

                    $minutes = floor($remaining / 60);
                    $seconds = $remaining % 60;

                    return response()->json([
                        'success' => 0,
                        'message' => sprintf(
                            'Please wait %02d:%02d before requesting a new OTP.',
                            $minutes,
                            $seconds
                        )
                    ], 429);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Generate New OTP
            |--------------------------------------------------------------------------
            */
            $otp = random_int(100000, 999999);
            $otp = 654321;
            $user->otp = Hash::make($otp);
            $user->otp_expiry = now()->addMinutes(5);
            $user->otp_attempts = 0;
            $user->otp_last_sent_at = now();
            $user->otp_resend_count++;
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Send OTP Email
            |--------------------------------------------------------------------------
            */
            MailService::sendOTP($user,$otp);

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */

            TblLogModel::create([
                'module'       => 'AUTH',
                'module_id'    => $user->mst_users_id,
                'mst_users_id' => $user->mst_users_id,
                'action'       => 'OTP_RESEND',
                'ip_address'   => $request->ip(),
                'remarks'      => 'Login OTP resent successfully.'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => 1,
                'message' => 'A new verification code has been sent to your registered email address.'
            ]);
        } catch (\Exception $e) {

            Log::error('OTP Resend Error : '.$e->getMessage());
            return response()->json([
                'success' => 0,
                'message' => 'Unable to resend OTP. Please try again later.'
            ], 500);

        }
    }

    public function verify(Request $request){
        try {
            $otp = $request->otp;
            /*
            |--------------------------------------------------------------------------
            | Validate Pending Login Session
            |--------------------------------------------------------------------------
            */
            if (!session()->has('pending_login') ||!session()->has('pending_user_id')) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Your login session has expired. Please login again.'
                ], 401);
            }
            
            /*
            |--------------------------------------------------------------------------
            | Validate Input
            |--------------------------------------------------------------------------
            */

            $validator = Validator::make($request->all(), ['otp' => ['required', 'digits:6']]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Get User
            |--------------------------------------------------------------------------
            */
            $user = MstUserModel::find(session('pending_user_id'));
            if (!$user) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return response()->json([
                    'success' => 0,
                    'message' => 'User not found.'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Check OTP Expiry
            |--------------------------------------------------------------------------
            */
            if (empty($user->otp_expiry) || now()->gt($user->otp_expiry)) {
                return response()->json([
                    'success' => 0,
                    'logout'   => true,
                    'redirect' => route('login'),
                    'message' => 'OTP has expired. Please request a new OTP.'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Maximum OTP Attempts Exceeded
            |--------------------------------------------------------------------------
            */
            if ($user->otp_attempts >= 5) {
                // Clear OTP information
                $user->otp = null;
                $user->otp_expiry = null;
                $user->otp_attempts = 0;
                $user->otp_last_sent_at = null;
                $user->otp_resend_count = 0;
                $user->save();
                
                // Audit Log
                TblLogModel::create([
                    'module'       => 'AUTH',
                    'module_id'    => $user->mst_users_id,
                    'mst_users_id' => $user->mst_users_id,
                    'action'       => 'OTP_BLOCKED',
                    'ip_address'   => $request->ip(),
                    'remarks'      => 'Maximum OTP verification attempts exceeded.'
                ]);

                // Destroy pending login session
                $request->session()->forget([
                    'pending_login',
                    'pending_user_id',
                    'pending_password_change',
                    'pending_device_fingerprint'
                ]);

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success'  => 0,
                    'logout'   => true,
                    'redirect' => route('login'),
                    'message'  => 'Maximum OTP attempts exceeded. Please login again.'
                ], 429);
            }
            /*
            |--------------------------------------------------------------------------
            | Verify OTP
            |--------------------------------------------------------------------------
            */
            if (!Hash::check($otp, $user->otp)) {
                $user->increment('otp_attempts');
                $remaining = 3 - $user->otp_attempts;
                /*
                |--------------------------------------------------------------------------
                | Maximum OTP Attempts Reached
                |--------------------------------------------------------------------------
                */
                if ($user->otp_attempts >= 3) {
                    // Clear OTP information
                    $user->otp = null;
                    $user->otp_expiry = null;
                    $user->otp_attempts = 0;
                    $user->otp_last_sent_at = null;
                    $user->otp_resend_count = 0;
                    $user->save();
                    
                    // Clear pending login session
                    $request->session()->forget([
                        'pending_login',
                        'pending_user_id',
                        'pending_password_change',
                        'pending_device_fingerprint'
                    ]);

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // Audit Log
                    TblLogModel::create([
                        'module'       => 'AUTH',
                        'module_id'    => $user->mst_users_id,
                        'mst_users_id' => $user->mst_users_id,
                        'action'       => 'OTP_FAILED',
                        'ip_address'   => $request->ip(),
                        'remarks'      => 'Maximum OTP verification attempts exceeded.'
                    ]);

                    return response()->json([
                        'success'  => 0,
                        'logout'   => true,
                        'redirect' => route('login'),
                        'message'  => 'Maximum OTP attempts exceeded. Please login again.'
                    ], 423);
                }

                return response()->json([
                    'success' => 0,
                    'message' => "Invalid OTP. {$remaining} attempt(s) remaining."
                ], 422);
            }

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
            | Get Role
            |--------------------------------------------------------------------------
            */

            $role = MstRoleModel::find(
                $user->mst_roles_id
            );           
            /*
            |--------------------------------------------------------------------------
            | Password Change Required
            |--------------------------------------------------------------------------
            */
            $passwordExpired = false;
            if (!empty($user->password_changed_at)) {
                $passwordExpired =
                    now()->diffInDays(
                        $user->password_changed_at
                    ) >= 90;
            }
            $mustChangePassword = $user->is_first_login == 1 || $user->force_password_change == 1 || $passwordExpired;

             /*
            |--------------------------------------------------------------------------
            | Create Login Session
            |--------------------------------------------------------------------------
            */
            session([
                'user_id'               => $user->mst_users_id,
                'username'              => $user->username,
                'email'                 => $user->email,
                'role_id'               => $user->mst_roles_id,
                'role_name'             => $role?->role_name,
                'is_logged_in'          => true,
                'is_first_login'        => $user->is_first_login,
                'must_change_password'  => $mustChangePassword,
                'profile_photo'         => $user->profile_photo
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */
            $user->session_id = session()->getId();
            $user->device_fingerprint = session('pending_device_fingerprint');
            $user->last_login = now();
            $user->otp = null;
            $user->otp_expiry = null;
            $user->otp_attempts = 0;
            $user->otp_last_sent_at = null;
            $user->otp_resend_count = 0;
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Clear Pending Session
            |--------------------------------------------------------------------------
            */
            session()->forget([
                'pending_login',
                'pending_user_id',
                'pending_password_change',
                'pending_device_fingerprint'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */
            TblLogModel::create([
                'module'       => 'AUTH',
                'module_id'    => $user->mst_users_id,
                'mst_users_id' => $user->mst_users_id,
                'action'       => 'OTP_VERIFIED',
                'ip_address'   => $request->ip(),
                'remarks'      => 'Two-factor authentication successful.'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => 1,
                'message' => 'Verification successful.',
                'redirect' => session('must_change_password')
                    ? route('change-password')
                    : route('main-dashboard')
            ]);

        } catch (Exception $e) {
            Log::error( 'OTP Verification Error : '.$e->getMessage());

            return response()->json([
                'success' => 0,
                'message' => 'Unable to verify OTP.'
            ], 500);

        }
    }
}
