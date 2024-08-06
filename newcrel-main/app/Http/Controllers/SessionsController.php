<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Logged;
use App\User;

use Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Auth;
use Cache;
use Session;
use DB;
class SessionsController extends Controller
{

    protected $decayMinutes = 10;
    protected $maxAttempts = 5;

    public function login(Request $request)
    {

        // 필수항목 검사
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|
                        regex: /^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|
                        regex:/^\S*$/u|
                        max:10'
        ],
        [
            'email.required' => '이메일이 입력되지 않았습니다.',
            'email' => '이메일 형식이 올바르지 않습니다.',
            'password.required' => '비밀번호가 입력되지 않았습니다.',
            'regex' => '비밀번호 형식이 올바르지 않습니다.',
            'min' => '비밀번호는 :min자 이상으로 입력 해주시기 바랍니다.',
            'max' => '비밀번호는 :max자 이하로 입력 해주시기 바랍니다.'
        ]);
        if($validator->fails()){
            $errors = $validator->errors();
            foreach ($errors->all() as $message){
                $res['msg'] = $message;
                break; // 오류 메세지중 상위 한개만 배열에 담음.
            }
            return response()-> json([
                'error'=> true,
                'msg'=> $res['msg']
            ]);
        }
        $identify = User::whereEmail($request->input('email'))->first();
        if(!$identify){
            return response()-> json([
                'error'=> true,
                'msg'=> '가입되지 않은 이메일입니다.'
            ]);
        }

        /**
         * 로그인 잠금 상태
         */
        $lockoutKey = 'lockout_'. $request->input('email');

        if(Cache::has($lockoutKey)) {
            return response()-> json([
                'error'=> true,
                'lock'=> 'wait'
            ]);
        }



        $token = Auth::guard('bduser')-> attempt($request->only('email', 'password'));
        // dd($token);
        if(!$token){
            /**
             * 로그인 실패
             * 로그인 5회 실패 10초 동안 잠금 처리
             */
            if(!Cache::has('maxAtt')){ // 최초 실패
                Cache::put('maxAtt', $this->maxAttempts);
            }else{
                $cnt = Cache::get('maxAtt');
                $cnt--;
                Cache::put('maxAtt', $cnt);
            }

            if(Cache::get('maxAtt') > 0){
                return response()-> json([
                    'error'=> true,
                    'msg'=> '아이디 또는 비밀번호가 일치하지 않습니다.<br>남은 시도 횟수: '. Cache::get('maxAtt')
                ]);
            }
            Cache::forget('maxAtt');
            Cache::put($lockoutKey, $this->decayMinutes , $this->decayMinutes);
            return response()-> json([
                'error'=> true,
                'msg'=> '<strong>10</strong>초 후에 다시 시도해주시기 바랍니다.',
                'lock'=> 10
            ]);
        }

        $user = Auth::guard('bduser')-> user();

        Logged::create([
            'user_id' => $user->id,
            'date_created' => Carbon::now(),
            'remote_ip' => $request->ip(),
            'version' => '1.0',
        ]);
        // mypage
        // $mypage = DB::connection('mysql_ori')->table('terms')
        //             ->leftJoin('user_interests', 'terms.user_id', '=', 'user_interests.user_id')
        //             ->leftJoin('user_role', 'user_role.user_id', '=', 'terms.user_id')
        //             ->leftJoin('role', 'role.id', '=', 'user_role.role_id')
        //             ->select('role.authority', 'terms.marketing', DB::raw('count(user_interests.pnu)as cnt'))
        //             ->where('terms.user_id', '=', $user->id)
        //             ->first();


        /**
         * 중복 로그인 체크 -> token 방식으로 함
         */
        $token = Str::random(60);
        $user->token = $token;
        $token_sessions = ['id'=>$user->id, 'token'=>$token]; //중복 로그인 관련
        Session::put('login_token',(object) $token_sessions);
        $user->save();

        // $user->marketing = $mypage->marketing;
        // $user->interests = $mypage->cnt;
        // $user->authority = $mypage->authority;

        // if($mypage->authority == 'MEMBER'){
        //     $authority = '일반회원';
        // }else if($mypage->authority == 'PREMIUM'){
        //     $authority = 'PREMIUM';
        // }

        // $data = [
        //     'email' => $user->email,
        //     'authority' => $authority,
        //     'interests' => $mypage->cnt,
        //     'marketing'=> $mypage->marketing,
        // ];

        return response()-> json([
            'error'=> false,
            'url'=> '/'
        ]);
    }
    public function destroy()
    {
        // session()->flush();
        Auth::guard('bduser')->logout();
        return redirect('/');
    }

}
