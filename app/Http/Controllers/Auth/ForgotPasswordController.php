<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\auth\TblPasswordHistoryModel;
use App\Models\auth\TblPasswordResetModel;
use App\Models\common\TblLogModel;
use App\Models\masters\MstUserModel;
use App\Services\Mail\MailService;
use App\Services\Security\RSAService;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
use Str;
use Validator;

class ForgotPasswordController extends Controller
{
    public function index(){
        return view('auth.forgot-password');
    }
    public function sendLink(Request $request){
        try {
            /*
            |--------------------------------------------------------------------------
            | Decrypt Email
            |--------------------------------------------------------------------------
            */

            $email = RSAService::decrypt($request->email);

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */
            $validator = Validator::make(['email' => $email],
                [
                    'email' => [
                        'required',
                        'email'
                    ]
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Find Active User
            |--------------------------------------------------------------------------
            */

            $user = MstUserModel::where('email', $email)
                ->where('is_active', 1)
                ->where('is_delete', 0)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Always Return Success
            |--------------------------------------------------------------------------
            |
            | Prevent Email Enumeration Attack
            |
            */

            if (!$user) {
                return response()->json([
                    'success' => 1,
                    'message' => 'If an account exists for this email address, a password reset link has been sent.'
                ]);
            }
           
            /*
            |--------------------------------------------------------------------------
            | Maximum 3 Reset Requests In 24 Hours
            |--------------------------------------------------------------------------
            */
            $resetCount = TblPasswordResetModel::where(
                    'mst_users_id',
                    $user->mst_users_id
                )
                ->where(
                    'create_date',
                    '>=',
                    now()->subDay()
                )
                ->count();
            if ($resetCount >= 3) {
                return response()->json([
                    'success' => 0,
                    'message' => 'You have reached the maximum password reset requests (3) within the last 24 hours. Please try again tomorrow or contact the administrator.'
                ], 429);

            }
            /*
            |--------------------------------------------------------------------------
            | Check Existing Active Reset Link
            |--------------------------------------------------------------------------
            */
            $activeReset = TblPasswordResetModel::where(
                    'mst_users_id',
                    $user->mst_users_id
                )
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest('tbl_password_resets_id')
                ->first();

            if ($activeReset) {
                $remaining = now()->diffInSeconds(
                    $activeReset->expires_at,
                    false
                );

                $remaining = max(0, (int) $remaining);
                $minutes = intdiv($remaining, 60);
                $seconds = $remaining % 60;
                $time = '';

                if ($minutes > 0) {
                    $time .= $minutes . ' minute' . ($minutes > 1 ? 's' : '');
                }

                if ($seconds > 0) {
                    if ($minutes > 0) {
                        $time .= ' ';
                    }
                    $time .= $seconds . ' second' . ($seconds > 1 ? 's' : '');
                }
                return response()->json([
                    'success' => 0,
                    'message' => "A password reset link has already been sent. Please wait {$time} before requesting another."
                ], 429);
            }
            /*
            |--------------------------------------------------------------------------
            | Generate Secure Token
            |--------------------------------------------------------------------------
            */

            $plainToken = Str::random(64);

            /*
            |--------------------------------------------------------------------------
            | Save Reset Request
            |--------------------------------------------------------------------------
            */
            TblPasswordResetModel::create([
                'mst_users_id' => $user->mst_users_id,
                'email' => $user->email,
                'token' => Hash::make($plainToken),
                'expires_at' => now()->addMinutes(10),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            /*
            |--------------------------------------------------------------------------
            | Send Reset Email
            |--------------------------------------------------------------------------
            */
            $mail = MailService::sendForgotPassword(
                $user,
                $plainToken
            );

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */
            TblLogModel::create([
                'module'       => 'AUTH',
                'module_id'    => $user->mst_users_id,
                'mst_users_id' => $user->mst_users_id,
                'action'       => 'PASSWORD_RESET_REQUEST',
                'ip_address'   => $request->ip(),
                'remarks'      => 'Password reset link sent.'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => 1,
                'message' => 'If an account exists for this email address, a password reset link has been sent.',
                'redirect' => route('login')
            ]);
        } catch (Exception $e) {
            Log::error('Forgot Password Error : '.$e->getMessage());
            return response()->json([
                'success' => 0,
                'message' => 'Unable to process your request.'
            ], 500);
        }
    }

    public function resetForm(string $token){
        try {
            /*
            |--------------------------------------------------------------------------
            | Find Valid Reset Requests
            |--------------------------------------------------------------------------
            */
            $resetRequests = TblPasswordResetModel::whereNull('used_at')
                ->where('expires_at', '>', now())
                ->get();
            $reset = null;
            /*
            |--------------------------------------------------------------------------
            | Verify Hashed Token
            |--------------------------------------------------------------------------
            */
            foreach ($resetRequests as $request) {
                if (Hash::check($token, $request->token)) {
                    $reset = $request;
                    break;
                }
            }
            /*
            |--------------------------------------------------------------------------
            | Invalid / Expired Token
            |--------------------------------------------------------------------------
            */
            if (!$reset) {
                return redirect()
                    ->route('forgot-password')
                    ->with(
                        'error',
                        'This password reset link is invalid or has expired.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Find User
            |--------------------------------------------------------------------------
            */
            $user = MstUserModel::find(
                $reset->mst_users_id
            );
            if (!$user) {
                return redirect()
                    ->route('forgot-password')
                    ->with(
                        'error',
                        'Invalid password reset request.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Show Reset Password Page
            |--------------------------------------------------------------------------
            */
            return view('auth.reset-password',['token' => $token,'user'  => $user]);
        } catch (Exception $e) {
            Log::error('Reset Password Form Error : ' .$e->getMessage());
            return redirect()
                ->route('forgot-password')
                ->with(
                    'error',
                    'Unable to process your request.'
                );
        }
    }

    public function reset(Request $request){
        DB::beginTransaction();
        try {
            /*
            |--------------------------------------------------------------------------
            | Decrypt Request
            |--------------------------------------------------------------------------
            */

            $token = $request->token;

            $newPassword = RSAService::decrypt(
                $request->new_password
            );

            $confirmPassword = RSAService::decrypt(
                $request->confirm_password
            );

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */
            $validator = Validator::make(
                [
                    'token' => $token,
                    'new_password' => $newPassword,
                    'confirm_password' => $confirmPassword
                ],

                [
                    'token' => 'required',

                    'new_password' => [
                        'required',
                        'min:8',
                        'regex:/[A-Z]/',
                        'regex:/[a-z]/',
                        'regex:/[0-9]/',
                        'regex:/[@$!%*#?&]/'
                    ],

                    'confirm_password' => [
                        'required',
                        'same:new_password'
                    ]
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            /*
            |--------------------------------------------------------------------------
            | Find Active Reset Token
            |--------------------------------------------------------------------------
            */
            $resetRequests = TblPasswordResetModel::whereNull('used_at')
            ->where('expires_at', '>', now())
            ->get();

            $reset = null;
            foreach ($resetRequests as $item) {
                if (Hash::check($token, $item->token)) {
                    $reset = $item;
                    break;
                }
            }
            if (!$reset) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Invalid or expired password reset link.'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Find User
            |--------------------------------------------------------------------------
            */
            $user = MstUserModel::find($reset->mst_users_id);
            if (!$user) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Invalid password reset request.'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Prevent Last 3 Password Reuse
            |--------------------------------------------------------------------------
            */
            $history = TblPasswordHistoryModel::where(
                'mst_users_id',
                $user->mst_users_id
            )
            ->latest('tbl_password_history_id')
            ->take(3)
            ->get();

            foreach ($history as $item) {
                if (Hash::check($newPassword,$item->password)) {
                    return response()->json([
                        'success' => 0,
                        'message' => 'You cannot reuse your last 3 passwords.'
                    ], 422);
                }
            }
            /*
            |--------------------------------------------------------------------------
            | Update Password
            |--------------------------------------------------------------------------
            */
            $user->password = Hash::make($newPassword);
            $user->password_changed_at = now();
            $user->force_password_change = 0;
            $user->is_first_login = 0;
            $user->session_id = null;
            $user->save();

             /*
            |--------------------------------------------------------------------------
            | Password History
            |--------------------------------------------------------------------------
            */
            TblPasswordHistoryModel::create([
                'mst_users_id' => $user->mst_users_id,
                'password' => $user->password
            ]);

            /*
            |--------------------------------------------------------------------------
            | Keep Last 3 Passwords
            |--------------------------------------------------------------------------
            */
            $historyIds = TblPasswordHistoryModel::where(
                    'mst_users_id',
                    $user->mst_users_id
                )
                ->orderByDesc('tbl_password_history_id')
                ->pluck('tbl_password_history_id')
                ->slice(3);

            if ($historyIds->count()) {
                TblPasswordHistoryModel::whereIn(
                    'tbl_password_history_id',
                    $historyIds
                )->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Mark Reset Link Used
            |--------------------------------------------------------------------------
            */
            $reset->used_at = now();
            $reset->save();
            /*
            |--------------------------------------------------------------------------
            | Remove Other Reset Links
            |--------------------------------------------------------------------------
            */
            TblPasswordResetModel::where(
                'mst_users_id',
                $user->mst_users_id
            )->where('tbl_password_resets_id','!=',$reset->tbl_password_resets_id)
            ->delete();

             /*
            |--------------------------------------------------------------------------
            | Send Notification Email
            |--------------------------------------------------------------------------
            */
            MailService::sendPasswordChanged(
                $user,
                $request->ip(),
                $request->userAgent()
            );

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */
            TblLogModel::create([
                'module' => 'AUTH',
                'module_id' => $user->mst_users_id,
                'mst_users_id' => $user->mst_users_id,
                'action' => 'PASSWORD_RESET',
                'ip_address' => $request->ip(),
                'remarks' => 'Password reset successfully.'
            ]);

            DB::commit();
            return response()->json([
                'success' => 1,
                'message' => 'Password has been reset successfully.',
                'redirect' => route('login')
            ]);
        }catch (Exception $e) {
            DB::rollBack();
            Log::error('Password Reset Error : '.$e->getMessage());
            return response()->json([
                'success' => 0,
                'message' => 'Unable to reset password.'
            ],500);
        }
    }
}
