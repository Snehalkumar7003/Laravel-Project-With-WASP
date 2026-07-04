<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\auth\TblPasswordHistoryModel;
use App\Models\common\TblLogModel;
use App\Models\masters\MstUserModel;
use App\Services\Mail\MailService;
use App\Services\Security\RSAService;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
use Validator;

class ChangePasswordController extends Controller
{
    public function index(){
        return view('verification.change-password');
    }

    public function update(Request $request){
        try{
            //  dd('Controller reached');
             
            DB::beginTransaction();
            /*
            |--------------------------------------------------------------------------
            | Decrypt Passwords
            |--------------------------------------------------------------------------
            */
            $currentPassword    = RSAService::decrypt($request->password);
            $newPassword        = RSAService::decrypt($request->new_password);
            $confirmPassword    = RSAService::decrypt($request->confirm_password);

            /*
            |--------------------------------------------------------------------------
            | Validate Input
            |--------------------------------------------------------------------------
            */
            $validator=Validator::make(
                [
                    'current_password'  =>  $currentPassword,
                    'new_password'      =>  $newPassword,
                    'confirm_password'  =>  $confirmPassword
                ],
                [
                    'current_password'  =>  'required',
                    'new_password'=>[
                        'required',
                        'min:8',
                        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
                    ],
                    'confirm_password'=>'required|same:new_password'
                ]
            );
            if($validator->fails()){
                return response()->json([
                    'csrfHash' => csrf_token(),
                    'success'   =>  0,
                    'message'   =>  $validator
                        ->errors()
                        ->first()
                ],422);
            }

            /*
            |--------------------------------------------------------------------------
            | Get User
            |--------------------------------------------------------------------------
            */
            $user   =   MstUserModel::find(session('user_id'));
            if(!$user){
                return response()->json([
                    'csrfHash' => csrf_token(),
                    'success'=>0,
                    'message'=>'User not found.'
                ],404);
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Current Password
            |--------------------------------------------------------------------------
            */
            if(!Hash::check($currentPassword,$user->password)){
                return response()->json([
                    'csrfHash' => csrf_token(),
                    'success'=>0,
                    'message'=>'Current password is incorrect.'
                ],422);
            }

            /*
            |--------------------------------------------------------------------------
            | Current Password Check
            |--------------------------------------------------------------------------
            */
            if(Hash::check($newPassword,$user->password)){
                return response()->json([
                    'csrfHash' => csrf_token(),
                    'success'=>0,
                    'message'=>'New password cannot be same as current password.'
                ],422);
            }

            /*
            |--------------------------------------------------------------------------
            | Check Last 3 Passwords
            |--------------------------------------------------------------------------
            */
            $history= TblPasswordHistoryModel::where('mst_users_id',$user->mst_users_id)
                ->latest('tbl_password_history_id')
                ->take(3)
                ->get();

            if ($history->isNotEmpty()) {
                foreach($history as $item){
                    if(Hash::check($newPassword,$item->password)){
                        return response()->json([
                            'csrfHash' => csrf_token(),
                            'success'=>0,
                            'message'=>'You cannot reuse your last 3 passwords.'
                        ],422);
                    }
                }
            }    
            /*
            |--------------------------------------------------------------------------
            | Update Password
            |--------------------------------------------------------------------------
            */
            $user->password = Hash::make($newPassword);
            $user->password_changed_at = now();
            $user->is_first_login = 0;
            $user->force_password_change = 0;
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Save Password History
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
            $historyIds = TblPasswordHistoryModel::where('mst_users_id',$user->mst_users_id)
                ->orderByDesc('tbl_password_history_id')
                ->offset(3)
                ->limit(PHP_INT_MAX)
                ->pluck('tbl_password_history_id');

            if($historyIds->count()){
                TblPasswordHistoryModel::whereIn(
                    'tbl_password_history_id',
                    $historyIds
                )->delete();
            }
            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */
            TblLogModel::create([
                'module' => 'AUTH',
                'module_id' => $user->mst_users_id,
                'mst_users_id' => $user->mst_users_id,
                'action' => 'PASSWORD_CHANGED',
                'ip_address' => request()->ip(),
                'remarks' => 'Password changed successfully.'
            ]);
             /*
            |--------------------------------------------------------------------------
            | Commit Transaction
            |--------------------------------------------------------------------------
            */
            DB::commit();
            /*
            |--------------------------------------------------------------------------
            | Send Email Notification
            |--------------------------------------------------------------------------
            */
            MailService::sendPasswordChanged($user,$request->ip(),$request->userAgent());
            /*
            |--------------------------------------------------------------------------
            | Logout User
            |--------------------------------------------------------------------------
            */
            $user->session_id = null;
            $user->device_fingerprint = null;
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Destroy Session
            |--------------------------------------------------------------------------
            */
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'csrfHash' => csrf_token(),
                'success' => 1,
                'message' => 'Password changed successfully. Please login again.',
                'redirect' => route('login')
            ]);

        }catch(Exception $e){
            DB::rollBack();
            Log::error('Change Password Error : '.$e->getMessage());

            return response()->json([
                'csrfHash' => csrf_token(),
                'success'=>0,
                'message'=>'Unable to change password.'
            ],500);
        }
    }
}
