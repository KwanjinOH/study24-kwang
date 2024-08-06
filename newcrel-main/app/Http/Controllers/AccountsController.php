<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MailsController;
use \Carbon\Carbon;
// use App\Http\Requests\RegisterRequest;

// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\QueryException as QueryException;
use Session;
use Validator;
use DB;

use App\User;
use App\Terms;
use App\UserRole;
use App\Detailes;


class AccountsController extends Controller
{
    // private $_terms;
    private $_rand;
    private $_randString;

    public function __construct() {
        // $this->_terms = null;
        $this->_rand = 0;
        $this->_randString = '';
    }

    // 비밀번호 찾기
    public function findView() {
        Session::put('cfm_res', false);
        Session::forget('email_cfm');
        // Session::flush(); // all remove session csrf 토큰도 초기화댐
        return view('crel.account.find');
    }
    public function findSend(Request $request) {
        $req = $request->input();

        $identify = User::whereEmail($req['email'])->first();
        if(!$identify){
            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg" style="text-align: center;">가입되지 않은 이메일입니다.</div><button class="close">확 인</button>'
            ]);
        }

        function isSecure() {
            return
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] == 443;
        }
        $proto = (isSecure())? 'https' : 'http';
	    $documentRoot = $proto.'://'.$_SERVER['HTTP_HOST'];


        $this->randomStringCreated();
        $sendData = [
            'code' => $this->_randString,
            'email' => $req['email'],
            'host' => $documentRoot
        ];
        $email_sessions = ['email'=>$req['email'], 'code'=>$this->_randString];
        Session::put('email_cfm', (object)$email_sessions);
        $res = (new MailsController)->FindEmailVerification($sendData);

        return $res;
    }
    public function resetView(){
        if(Session::get('cfm_res')){
            return view('crel.account.reset');
        }else{
            return redirect('/');
        }
    }
    public function reset(Request $request) {
        // DB::connection('mysql_ori')->enableQueryLog();

        // 인증번호 확인 검사
        if(!Session::get('cfm_res')){
            $res['msg'] = '<div class="layer-msg" style="text-align: center;">인증번호 확인이 되지 않았습니다.</div><button class="close">확 인</button>';
            return response()-> json([
                'error'=> true,
                'msg'=> $res['msg']
            ]);
        }
        // 필수항목 검사
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'pass' => 'required|confirmed|min:8|
                        regex: /^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|
                        regex:/^\S*$/u|
                        max:10'
        ],
        [
            'required' => '<div class="layer-msg" style="text-align: center;">입력되지 않은 정보가 있습니다.</div><button class="close">확 인</button>',
            'confirmed' => '<div class="layer-msg" style="text-align: center;">비밀번호가 일치하지 않습니다.</div><button class="close">확 인</button>',
            'regex' => '<div class="layer-msg" style="text-align: center;">비밀번호 형식이 올바르지 않습니다.</div><button class="close">확 인</button>',
            'min' => '<div class="layer-msg" style="text-align: center;">비밀번호는 :min자 이상으로 입력 해주시기 바랍니다.</div><button class="close">확 인</button>',
            'max' => '<div class="layer-msg" style="text-align: center;">비밀번호는 :max자 이하로 입력 해주시기 바랍니다.</div><button class="close">확 인</button>'
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

        $passwordSHA = DB::select(DB::raw("SELECT `EBGA_CREATE_PW_SHA`('".$request->input('pass')."')as sha"));

        try {
            $user = User::whereEmail(Session::get('email_cfm')->email)
                    ->update([
                        'password'=> $passwordSHA[0]->sha,
                        'uptate_ip'=> $request->ip(),
                        'update_date'=> Carbon::now(),
                        'password_changed'=> Carbon::now()
                    ]);

            // dd(DB::connection('mysql_ori')->getQueryLog()); // DB query 확인
        } catch (QueryException $e){
            // dd($e->errorInfo);
            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }

        Session::flush(); // all remove session

        return response()-> json([
            'error'=> false,
            'msg'=> '<div class="layer-msg" style="text-align: center;">비밀번호가 변경되었습니다.</div><a href="/"><button style="background-color: #fbb900; color: #fff" font-size: 14px>홈으로</button></a>',
            'url'=> '/'
        ]);

    }



    public function termsView() {
        // dd($this->_terms);
        return view('crel.account.terms');
    }
    public function termsSave(Request $request) {
        // session terms controll~

        $terms = $request->input();
        try {
            Session::put('terms', (object)$terms);
        } catch (Exception $ex) {
            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }

        return response()-> json([
            'error'=> false,
            'url'=>'/account/sign'
        ]);
    }

    public function signView(){
        if(Session::get('terms')){
            return view('crel.account.sign');
        }else{
            return redirect('/');
        }

    }

    public function sendMail(Request $request) {

        $req = $request->input();

        $duplicate = User::whereEmail($req['email'])->first();
        if($duplicate){
            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg" style="text-align: center;">이미 가입된 이메일입니다.</div><button class="close">확 인</button>'
            ]);
        }

        function isSecure() {
            return
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] == 443;
        }
        $proto = (isSecure())? 'https' : 'http';
	    $documentRoot = $proto.'://'.$_SERVER['HTTP_HOST'];


        $this->randomNumberCreated();
        $sendData = [
            'code' => $this->_rand,
            'email' => $req['email'],
            'host' => $documentRoot
        ];
        $email_sessions = ['email'=>$req['email'], 'code'=>$this->_rand];
        Session::put('email_cfm', (object)$email_sessions);
        Session::put('cfm_res', false);
        $res = (new MailsController)->SignEmailVerification($sendData);

        return $res;
        // DataBase insert pin code
    }

    public function verification(Request $request) {
        if(Session::get('cfm_res')){
            return response()-> json([
                'error'=> false,
                'msg' => '<div class="layer-msg" style="text-align: center;">이미 인증이 확인 되었습니다.</div><button class="close">확 인</button>',
            ]);
        }

        if(!Session::get('email_cfm')){
            return response()-> json([
                'error'=> true,
                'msg' => '이메일로 인증 문자를 확인해 주시기 바랍니다.</div>',
            ]);
        }

        $cfm = $request->input();
        // dd($cfm);
        if($cfm['type'] == 'sign'){
            $string = '인증번호';
            $code = (int)$cfm['code'];
            $_code = (int)Session::get('email_cfm')->code;

        }else if($cfm['type'] == 'find'){
            $string = '인증문자';
            $code = $cfm['code'];
            $_code = Session::get('email_cfm')->code;
        }


        if($_code != $code) {
            Session::put('cfm_res', false);
            return response()-> json([
                'error'=> true,
                'msg'=> '잘못된 '. $string .'입니다.'
            ]);
        }
        if(Session::get('email_cfm')->email != $cfm['email']) {
            Session::put('cfm_res', false);
            return response()-> json([
                'error'=> true,
                'msg'=> '이메일 정보와 '. $string .'가 일치하지 않습니다.'
            ]);
        }

        // Session::forget('email_cfm');

        Session::put('cfm_res', true);
        return response()-> json([
            'error'=> false,
            'msg' => '<div class="layer-msg" style="text-align: center;">'. $string .'가 확인 되었습니다.</div><button class="close">확 인</button>',
            // 'url' => ($cfm['type'] == 'find')? '/account/reset':false
        ]);
    }

    public function signRegist(Request $request) {

        $validatorResult = $this->signValidators($request);

        if($validatorResult['error']){
            return response()-> json([
                'error'=> $validatorResult['error'],
                'msg'=> $validatorResult['msg']
            ]);
        };

        $passwordSHA = DB::select(DB::raw("SELECT `EBGA_CREATE_PW_SHA`('".$request->input('pass')."')as sha"));

        DB::connection('mysql_ori')->beginTransaction(); // db 트랜잭션
        try {
            $user = User::create([
                // 'email'=> $request->input('email'),
                'email'=> Session::get('email_cfm')->email,
                'password'=> $passwordSHA[0]->sha,
                'password_expired'=> 1,
                'name'=> null,
                'create_ip'=> $request->ip(),
                'create_date'=> Carbon::now(),
                'uptate_ip'=> $request->ip(),
                'update_date'=> Carbon::now(),
                'password_changed'=> null,
                'version'=> '1.0'
            ]);

            // 트랜잭션 추가
            Terms::create([
                'user_id'=> $user->id,
                'utiliztion'=> 1,
                'personal'=> 1,
                'location'=> 1,
                'marketing'=> Session::get('terms')->mk,
                'update_date'=> Carbon::now(),
                'version'=> '1.0'
            ]);

            UserRole::create([
                'user_id'=> $user->id,
                'role_id'=> 5,
            ]);
            Detailes::create([
                'user_id'=> $user->id
            ]);

            DB::connection('mysql_ori')->commit(); // 성공시 커밋
        } catch (QueryException $e){
            DB::connection('mysql_ori')->rollback(); // 실패시 실행됐던 트랜잭션 모두 롤백

            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }

        // try {
        //     Terms::create([
        //         'user_id'=> $user->id,
        //         'utiliztion'=> 1,
        //         'personal'=> 1,
        //         'location'=> 1,
        //         'marketing'=> Session::get('terms')->mk,
        //         'update_date'=> Carbon::now(),
        //         'version'=> '1.0'
        //     ]);
        // } catch (QueryException $e){
        //     // dd($e->errorInfo);
        //     return response()-> json([
        //         'error'=> true,
        //         'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다(1).</div><button class="close">확 인</button>'
        //     ]);
        // }
        // try {
        //     UserRole::create([
        //         'user_id'=> $user->id,
        //         'role_id'=> 3,
        //     ]);
        // } catch (QueryException $e){
        //     return response()-> json([
        //         'error'=> true,
        //         'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다(2).</div><button class="close">확 인</button>'
        //     ]);
        // }
        // user details add 230425
        // try {
        //     Details::create([
        //         'user_id'=> $user->id
        //     ]);
        // } catch (QueryException $e){
        //     return response()-> json([
        //         'error'=> true,
        //         'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다(2).</div><button class="close">확 인</button>'
        //     ]);
        // }
        Session::flush(); // all remove session

        return response()-> json([
            'error'=> false,
            'msg'=> '<div class="layer-msg"><strong>'. $user->email .'님</strong><br>가입을 환영합니다.</div><a href="/"><button style="background-color: #fbb900; color: #fff" font-size: 14px>홈으로</button></a>',
            'url'=> '/'
        ]);

    }

    protected function randomStringCreated(){
        $this->_randString = bin2hex(random_bytes(4));
    }
    protected function randomNumberCreated()
    {
        $this->_rand = random_int(1000, 9999);
    }
    protected function signValidators($request){

        $res['error'] = true;

        // 필수항목 검사
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email|max:255',
                    'pass' => 'required|confirmed|min:8|
                                regex: /^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/|
                                regex:/^\S*$/u|
                                max:10'
                ],
                [
                    'required' => '<div class="layer-msg" style="text-align: center;">입력되지 않은 정보가 있습니다.</div><button class="close">확 인</button>',
                    'confirmed' => '<div class="layer-msg" style="text-align: center;">비밀번호가 일치하지 않습니다.</div><button class="close">확 인</button>',
                    'regex' => '<div class="layer-msg" style="text-align: center;">비밀번호 형식이 올바르지 않습니다.</div><button class="close">확 인</button>',
                    'min' => '<div class="layer-msg" style="text-align: center;">비밀번호는 :min자 이상으로 작성 해주시기 바랍니다.</div><button class="close">확 인</button>',
                    'max' => '<div class="layer-msg" style="text-align: center;">비밀번호는 :max자 이하로 작성 해주시기 바랍니다.</div><button class="close">확 인</button>'
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            foreach ($errors->all() as $message){
                $res['msg'] = $message;
                break; // 오류 메세지중 상위 한개만 배열에 담음.
            }
            return $res;
        }
        // 인증번호 확인 검사
        if(!Session::get('cfm_res')){
            $res['msg'] = '<div class="layer-msg" style="text-align: center;">인증번호 확인이 되지 않았습니다.</div><button class="close">확 인</button>';
            return $res;
        }

        // 약관 검사
        if(!Session::get('terms')){
            $res['msg'] = '<div class="layer-msg" style="text-align: center;">잘못된 접근 방식입니다.</div><button class="close">확 인</button>';

            return $res;
        }

        return $res['error'] = false;

    }

}
