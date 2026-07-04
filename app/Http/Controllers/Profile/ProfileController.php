<?php

namespace App\Http\Controllers\profile;

use App\Http\Controllers\Controller;
use App\Models\common\TblLogModel;
use App\Models\masters\MstUserModel;
use App\Services\Security\RSAService;
use DB;
use Exception;
use File;
use Illuminate\Http\Request;
use Log;
use Validator;

class ProfileController extends Controller{
    public function index(){
        /*
        |--------------------------------------------------------------------------
        | Load Logged In User
        |--------------------------------------------------------------------------
        */
        $user = MstUserModel::find(session('user_id'));
        return view('profile.profile',compact('user'));
    }

    public function update(Request $request){
        DB::beginTransaction();
        try {

            /*
            |--------------------------------------------------------------------------
            | Get Logged In User
            |--------------------------------------------------------------------------
            */
            $user = MstUserModel::find(session('user_id'));

            if (!$user) {
                return response()->json(['success' => 0,'message' => 'User not found.', 'csrfHash' => csrf_token()], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Decrypt Request
            |--------------------------------------------------------------------------
            */
            $email = RSAService::decrypt($request->email);
            $mobile = RSAService::decrypt($request->mobile);

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */
            $validator = Validator::make(
                [
                    'username' => $request->username,
                    'email'    => $email,
                    'mobile'   => $mobile,
                    'profile_image' => $request->file('profile_image')
                ],[
                    'username' => [
                        'required',
                        'min:3',
                        'max:150',
                        'regex:/^[A-Za-z]+(?: [A-Za-z]+)*$/'
                    ],

                    'email' => [
                        'required',
                        'email'
                    ],

                    'mobile' => [
                        'required',
                        'digits_between:10,13'
                    ],

                    'profile_image' => [
                        'nullable',
                        'image',
                        'mimes:jpg,jpeg,png,gif,webp',
                        'max:2048'
                    ]
                ],
                [
                    'username.regex' =>
                    'Name may contain only letters and spaces.'
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'message' => $validator->errors()->first(),
                    'csrfHash' => csrf_token()
                ], 422);
            }
            /*
            |--------------------------------------------------------------------------
            | Upload Profile Image
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('profile_image')) {
                /*
                |--------------------------------------------------------------------------
                | Delete Old Image
                |--------------------------------------------------------------------------
                */
                if (!empty($user->profile_photo) && File::exists(
                        public_path(
                            'uploads/profile/' .
                            $user->profile_photo
                        )
                    )
                ) {
                    File::delete(
                        public_path(
                            'uploads/profile/' .
                            $user->profile_photo
                        )
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Save New Image
                |--------------------------------------------------------------------------
                */
                $file = $request->file('profile_image');
                $fileName = 'USR_' . $user->mst_users_id .'_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $file->move(
                    public_path('uploads/profile'),
                    $fileName
                );
                $user->profile_photo = $fileName;
            }

            /*
            |--------------------------------------------------------------------------
            | Update Profile
            |--------------------------------------------------------------------------
            */
            $user->username = trim($request->username);
            $user->mobile = $mobile;
            $user->last_update = now();
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */

            TblLogModel::create([
                'module'       => 'PROFILE',
                'module_id'    => $user->mst_users_id,
                'mst_users_id' => $user->mst_users_id,
                'action'       => 'PROFILE_UPDATED',
                'ip_address'   => $request->ip(),
                'remarks'      => 'Profile updated successfully.'
            ]);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Profile updated successfully.',
                'csrfHash' => csrf_token()
            ]);
        }catch (Exception $e) {
            DB::rollBack();
            Log::error('Profile Update Error : ' .$e->getMessage());
            return response()->json([
                'success' => 0,
                'message' => 'Unable to update profile.',
                'csrfHash' => csrf_token()
            ], 500);
        }   
    }

    public function activity(Request $request){
        /*
        |--------------------------------------------------------------------------
        | Get Offset
        |--------------------------------------------------------------------------
        */

        $offset = $request->integer('offset', 0);

        /*
        |--------------------------------------------------------------------------
        | Load Activity Logs
        |--------------------------------------------------------------------------
        */

        $logs = TblLogModel::where('mst_users_id',session('user_id'))
            ->orderByDesc('tbl_logs_id')
            ->offset($offset)
            ->limit(20)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Transform Data
        |--------------------------------------------------------------------------
        */
        $logs->transform(function ($log) {
            $log->display_date = displayDateTime($log->create_date);
            $log->icon = $this->icon($log->action);
            $log->color = $this->color($log->action);
            return $log;
        });
        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'success'  => 1,
            'data'     => $logs,
            'hasMore'  => $logs->count() === 20,
            'csrfHash' => csrf_token()
        ]);
    }

    private function icon(string $action): string{
        return match ($action) {

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            'LOGIN'                   => 'log-in',
            'LOGOUT'                  => 'log-out',
            'OTP_LOGOUT'              => 'log-out',

            /*
            |--------------------------------------------------------------------------
            | OTP
            |--------------------------------------------------------------------------
            */

            'OTP_VERIFIED'            => 'shield-check',

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            'PASSWORD_CHANGED'        => 'key',
            'PASSWORD_RESET'          => 'refresh-ccw',
            'PASSWORD_RESET_REQUEST'  => 'mail',

            /*
            |--------------------------------------------------------------------------
            | Profile
            |--------------------------------------------------------------------------
            */

            'PROFILE_UPDATED'         => 'user',

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */

            'EMAIL_SENT'              => 'mail',

            default                   => 'activity',

        };
    }

    private function color(string $action): string{
        return match ($action) {

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            'LOGIN'                  => 'emerald',
            'LOGOUT'                 => 'rose',
            'OTP_LOGOUT'             => 'rose',

            /*
            |--------------------------------------------------------------------------
            | OTP
            |--------------------------------------------------------------------------
            */

            'OTP_VERIFIED'           => 'green',

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            'PASSWORD_CHANGED'       => 'amber',
            'PASSWORD_RESET'         => 'violet',
            'PASSWORD_RESET_REQUEST' => 'blue',

            /*
            |--------------------------------------------------------------------------
            | Profile
            |--------------------------------------------------------------------------
            */

            'PROFILE_UPDATED'        => 'cyan',

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */

            'EMAIL_SENT'             => 'indigo',

            default                  => 'primary',

        };
    }
    
}
