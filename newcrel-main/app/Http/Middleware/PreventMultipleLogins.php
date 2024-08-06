<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Support\Facades\Auth;
use Session;

class PreventMultipleLogins
{
    //중복 로그인 관련
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected $auth;

    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        // 로그인한 사용자의 정보를 가져옵니다.
        $user = $this->auth->guard('bduser')->user();
        // 세션에 사용자 정보가 없으면 로그인하지 않은 상태입니다.
        if (!$user) {
            // 기본 라우트로 이동합니다.
            return $next($request);
            // return redirect('/');
        }

        // 세션에 사용자 정보가 있으면 중복 로그인입니다.
        // 로그아웃 처리를 합니다.
        $login_token = Session::get('login_token');

        if($user->id !== $login_token->id){
            Auth::guard('bduser')->logout();
            throw new AuthenticationException('잘못된 접근입니다.');
        }
        if ($user->token !== $login_token->token) {
            Auth::guard('bduser')->logout();
            throw new AuthenticationException('중복 로그인되어 로그아웃 처리되었습니다.');
        }
        return $next($request);
    }
}
