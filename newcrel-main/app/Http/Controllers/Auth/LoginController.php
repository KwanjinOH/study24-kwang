<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /**
     * 230330 로그인 5회 실패 잠금 처리
     *
     */
    use ThrottlesLogins;


    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * 최대 시도 횟수 및 계정 잠금 시간 메서드 추가
     *
     */
    public function maxAttempts()
    {
        return 5;
    }
    public function decayMinutes()
    {
        return 10;
    }

    /**
     * 로그인 시도 횟수 확인
     *
     */
    protected function hasTooManyLoginAttempts(Requset $request)
    {
        $attempts = $this->limiter()->attempts($this->throttleKey($request));

        if($attempts >= $this->maxAttempts()) {
            $this->fireLockoutEvent($request);

            return true;
        }

        return false;
    }

    /**
     * 계정이 잠긴 상태일 떄 보내는 응답 정의
     *
     */

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableln(
            $this->throttleKey($request)
        );

        return dd('error');
    }
}
