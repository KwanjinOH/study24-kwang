@extends('crel.account.master')

@section('title', '약관동의')
@section('script', '/js/crel/account/terms.js?v=230221')

@section('terms')
    @parent

    <div class="acc-warp">
        <div class="acc-h">
            <h1>약관동의</h1>
        </div>
        {{-- <form action="/account/terms" method="post" enctype="multipart/form-data"> --}}
            {{-- @csrf --}}
            <div class="acc trm">
                <ul>
                    <li>
                        <div>
                            <input id="t-chk-all" type="checkbox"/>
                            <label for="t-chk-all">
                                <span class="t-all">전체 동의</span>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input id="t-chk-ut" class="selecters es" name="trm[]" value="ut" type="checkbox"/>
                            <label for="t-chk-ut">
                                <span class="es">[필수]</span> <a href="javascript:;">이용약관 동의</a>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input id="t-chk-pi" class="selecters es" name="trm[]" value="pi" type="checkbox"/>
                            <label for="t-chk-pi">
                                <span class="es">[필수]</span> <a href="javascript:;">개인정보 수집ㆍ이용 동의</a>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input id="t-chk-lo" class="selecters es" name="trm[]" value="lo" type="checkbox"/>
                            <label for="t-chk-lo">
                                <span class="es">[필수]</span> <a href="javascript:;">위치기반 서비스약관 동의</a>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div>
                            <input id="t-chk-mk" class="selecters un" name="ch[]" value="mk" type="checkbox"/>
                            <label for="t-chk-mk">
                                <span class="un">[선택]</span> 마케팅 정보 수신 동의
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="acc btn">
                {{-- <a href="/account/sign"> --}}
                    <button id="afterBtn" type="button">동의 후 계속</button>
                {{-- </a> --}}
            </div>
        {{-- </form> --}}
    </div>

@endsection

