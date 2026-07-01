<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Profile\ChangePasswordController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
|
| Accessible only when user is NOT authenticated.
|
*/

Route::middleware('guest')->group(function () {
    
    // login
    Route::get('/', [LoginController::class, 'index'])
        ->name('login');
    Route::post('/auth/validate-login', [LoginController::class, 'validateLogin'])
        ->name('validate-login');

    //public key        
    Route::get('/auth/public-key', [LoginController::class, 'publicKey']);

    // Forgot Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])
        ->name('forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class,'sendLink'])
    ->name('forgot-password.send');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class,'resetForm'])
        ->name('reset-password');
    Route::post('/reset-password', [ForgotPasswordController::class,'reset'])
        ->name('reset-password.update');

});
/*
|--------------------------------------------------------------------------
| OTP Verification Routes
|--------------------------------------------------------------------------
|
| Accessible only after successful username/password validation.
|
*/

Route::middleware(['otp','no.cache'])->group(function () {
    Route::get('/otp', [OtpController::class, 'index'])
        ->name('otp.index');
    Route::post('/otp/verify', [OtpController::class, 'verify'])
        ->name('otp.verify');
    Route::post('/otp/resend', [OtpController::class, 'resend'])
        ->name('otp.resend');
});


/*
|--------------------------------------------------------------------------
| Protected Application Routes
|--------------------------------------------------------------------------
|
| Accessible only after successful login + OTP verification.
|
*/

Route::prefix('app')
    ->middleware(['session.security','no.cache'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/main-dashboard', [DashboardController::class, 'index'])
            ->name('main-dashboard');

        /*
        |--------------------------------------------------------------------------
        | Change Password
        |--------------------------------------------------------------------------
        */

        Route::get('/change-password', [ChangePasswordController::class, 'index'])
            ->name('change-password');

        Route::post('/change-password', [ChangePasswordController::class, 'update'])
            ->name('change-password.update');
    });

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::get('/logout', [LoginController::class,'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Testing routes
|--------------------------------------------------------------------------
*/
// Route::get('/test-mail', function () {
//     $user = App\Models\masters\MstUserModel::first();
//     return App\Services\Mail\MailService::sendOTP(
//         $user,
//         "123456"
//     );

// });