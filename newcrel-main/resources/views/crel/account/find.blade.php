@extends('crel.account.master')

@section('title', '비밀번호 찾기')
@section('script', '/js/crel/account/find.js?v=230322')

@section('find')
    @parent

    <div class="acc-warp">
        <div class="acc-h">
            <h1>비밀번호 찾기 & 변경</h1>
        </div>
        <form id="resetForm">
        @csrf
            <div class="acc ip">
                <div class="cfm-auth">
                    <label for="find-email">이메일</label>
                    <div class="unit">
                        @guest('bduser')
                            <input id="find-email" type="email" name="email" autocomplete="email"  placeholder="이메일 주소"/>
                        @endguest
                        @auth('bduser')
                            <input id="find-email" type="email" name="email" autocomplete="email"  placeholder="이메일 주소" value="{{ auth('bduser')->user()->email }}" readonly/>
                        @endauth
                        <button id="find-send" class="send" type="button">인증문자 전송</button>
                        {{-- <button type="button">재전송</button> --}}
                    </div>
                    <div id="auth-string" class="unit">
                        <input id="find-string" type="text" name="auth-string" autocomplete="username" placeholder="인증문자 8자리를 입력해 주시기 바랍니다."/>
                        <button id="find-cfm-btn" class="cfm-chk" type="button">확인</button>
                    </div>
                </div>

                <div class="unit-warp">
                    <label>변경할 비밀번호</label>
                    <input id="pass" type="password" name="pass"  autocomplete="new-password" placeholder="비밀번호(8자 이상, 10자 이하, 영문/숫자 혼합)"/>
                </div>
                <div class="unit-warp">
                    <label>변경할 비밀번호 확인</label>
                    <input id="pass-cfm" type="password" name="pass_confirmation" autocomplete="new-password" placeholder="비밀번호를 재 입력해 주시기 바랍니다."/>
                </div>
            </div>
            <div class="acc btn">
                <button id="reset-submit" type="button">비밀번호 변경</button>
            </div>
        </form>


    </div>

@endsection

