{{-- @extends('crel.account.master')

@section('title', '비밀번호 변경')
@section('script', '/js/crel/account/reset.js?v=230324')

@section('reset')
    @parent

    <div class="acc-warp">
        <div class="acc-h">
            <h1>비밀번호 변경</h1>
        </div>
        <form id="resetForm">
        @csrf
            <div class="acc ip">
                    <div class="unit-warp">
                        <label>비밀번호</label>
                        <input id="pass" type="password" name="pass"  autocomplete="new-password" placeholder="비밀번호(8자 이상, 10자 이하, 영문/숫자 혼합)"/>
                    </div>
                    <div class="unit-warp">
                        <label>비밀번호 확인</label>
                        <input id="pass-cfm" type="password" name="pass_confirmation" autocomplete="new-password" placeholder="비밀번호를 재 입력해 주시기 바랍니다."/>
                    </div>
            </div>

            <div class="acc btn">
                <button id="reset-submit" type="button">비밀번호 변경</button>
            </div>
        </form>
    </div>

@endsection
 --}}
