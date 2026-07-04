<?php

namespace App\Http\Middleware\Security;

use App\Models\masters\MstUserModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request,Closure $next): Response {

        /*
        |--------------------------------------------------------------------------
        | User Not Logged In
        |--------------------------------------------------------------------------
        */
        if (!session('is_logged_in')) {
            return redirect()
                ->route('login')
                ->with('success','Please login to continue.');
        }

        /*
        |--------------------------------------------------------------------------
        | User Not Found
        |--------------------------------------------------------------------------
        */
        $user = MstUserModel::find(session('user_id'));

        if (!$user) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
            ->with(
                'error',
                'Your account no longer exists.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Single Device Login Validation
        |--------------------------------------------------------------------------
        */
        // if ($user->session_id !== $request->session()->getId()) {
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();
        //     return redirect()
        //         ->route('login')
        //         ->with(
        //             'error',
        //             'Your account was logged in from another device.1'
        //         );
        // }

        /*
        |--------------------------------------------------------------------------
        | Device Fingerprint Validation
        |--------------------------------------------------------------------------
        */
        $currentFingerprint =$request->header('X-DEVICE-FINGERPRINT');
        if (
            !empty($user->device_fingerprint) &&
            !empty($currentFingerprint) &&
            $user->device_fingerprint !==
            $currentFingerprint
        ) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your account was logged in from another device.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Session Timeout (15 Minutes)
        |--------------------------------------------------------------------------
        */
        if (
            session()->has('last_activity')
        ) {

            $idleMinutes =
                now()->diffInMinutes(
                    session('last_activity')
                );

            if ($idleMinutes >= 15) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'Your session has expired due to inactivity.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update Last Activity
        |--------------------------------------------------------------------------
        */
        session([
            'last_activity' => now()
        ]);
    
        /*
        |--------------------------------------------------------------------------
        | Force Password Change
        |--------------------------------------------------------------------------
        */
        if (
            session('must_change_password')
            &&
            !$request->routeIs('change-password')
            &&
            !$request->routeIs('change-password.update')
            &&
            !$request->routeIs('logout')
        ) {

            return redirect()
                ->route('change-password');
        }

        /*
        |--------------------------------------------------------------------------
        | Rotate Session Every Request
        |--------------------------------------------------------------------------
        */
        // $request->session()->migrate(true);
        // $request->session()->regenerateToken();
        
        return $next($request);
    }
}
