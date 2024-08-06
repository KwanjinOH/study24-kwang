@extends('crel.account.master')

@section('title', '회원가입')
@section('script', '/js/crel/account/sign.js?v=230222')

@section('sign')
    @parent

    <div class="acc-warp">
        <div class="acc-h">
            <h1>회원가입</h1>
        </div>
        <form id="signForm">
        @csrf
            <div class="acc ip">
                <div class="cfm-auth">
                    <label for="sign-email">이메일</label>
                    <div class="unit">
                        <input id="sign-email" type="email" name="email" autocomplete="email"  placeholder="이메일 주소"/>
                        <button id="auth-send" class="send" type="button">인증번호 전송</button>
                        {{-- <button type="button">재전송</button> --}}
                    </div>
                    <div id="auth-num" class="unit hide">
                        <input id="sign-number" type="number" placeholder="인증번호 4자리를 입력해 주시기 바랍니다."/>
                        <button id="cfm-btn" class="cfm-chk" type="button">확인</button>
                    </div>
                </div>
                {{-- <div class="error-msg">
                    <div>
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>잘못된 인증번호입니다.</span>
                    </div>
                </div> --}}
                <div class="unit-warp">
                    <label>비밀번호</label>
                    <input id="pass" type="password" name="pass"  autocomplete="new-password" placeholder="비밀번호(8자 이상, 10자 이하, 영문/숫자 혼합)"/>
                </div>
                <div class="unit-warp">
                    <label>비밀번호 확인</label>
                    <input id="pass-cfm" type="password" name="pass_confirmation" autocomplete="new-password" placeholder="비밀번호를 재 입력해 주시기 바랍니다."/>
                </div>
            </div>

        {{-- <div class="acc cfm">
            <div>
                <input type="number"/>
                <a>인증번호 확인</a>
            </div>
        </div> --}}
        <div class="acc btn">
            {{-- <a href="/account/sign"> --}}
                <button id="sign-submit" type="button">간편 가입</button>
            {{-- </a> --}}
        </div>
        </form>
    </div>
@endsection
