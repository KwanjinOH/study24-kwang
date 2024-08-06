<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class LoginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Auth::guard('bduser')->check()) {
            return response()-> json([
                'error'=> 401,
                'msg'=> '로그인이 필요한 서비스입니다.',
            ]);
        }
        return $next($request);
    }
}
