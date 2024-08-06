<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SignAuthSent;
use App\Mail\FindPasswordSent;
use Mail;

class MailsController extends Controller
{
    public function SignEmailVerification($details)
    {
        try {
            Mail::to($details['email'])->send(new SignAuthSent($details));
            return response()-> json([
                'error'=> false,
                'msg'=> '<div class="layer-msg">메일이 전송 되었습니다.<br><strong>인증번호</strong>를 확인하시어<br>아래 <strong>인증번호</strong> 확인란에 입력 바랍니다.</div><button class="close">확 인</button>'
            ]);
        } catch (Exception $ex) {
            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg"><strong class="str">메일이 전송이 되지 않았습니다.</strong><br>이메일 주소를 다시 확인해 보시고<br>전송이 되지 않을 시<br>문의해 주시기 바랍니다.</div><div class="inquiry">※문의전화 : 02-9999-9999</div><button class="close">확 인</button>'
            ]);
        }

    }
    public function FindEmailVerification($details)
    {
        try {
            Mail::to($details['email'])->send(new FindPasswordSent($details));
            return response()-> json([
                'error'=> false,
                'msg'=> '<div class="layer-msg">메일이 전송 되었습니다.<br><strong>인증문자</strong>를 확인하시어<br>아래 <strong>인증문자</strong> 확인란에 입력 바랍니다.</div><button class="close">확 인</button>'
            ]);
        } catch (Exception $ex) {
            return response()-> json([
                'error'=> true,
                'msg'=> '<div class="layer-msg"><strong class="str">메일이 전송이 되지 않았습니다.</strong><br>이메일 주소를 다시 확인해 보시고<br>전송이 되지 않을 시<br>문의해 주시기 바랍니다.</div><div class="inquiry">※문의전화 : 02-9999-9999</div><button class="close">확 인</button>'
            ]);
        }
    }
}
