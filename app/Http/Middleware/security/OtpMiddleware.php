<?php

namespace App\Http\Middleware\security;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OtpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next){
        if (!session('pending_login') || !session('pending_user_id')) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your login session has expired.'
                );
        }
        return $next($request);
    }
}
