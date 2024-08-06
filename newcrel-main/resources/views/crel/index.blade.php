<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css/common/common.css">
    <link rel="stylesheet" href="/css/crel/index.css" version=221130>
    <link rel="stylesheet" href="/css/crel/layer.css" version=221130>
    <link rel="stylesheet" href="/css/crel/mypage.css" version=230414>


    <title>부동산도서관</title>
</head>
<body>
    <header class="top-header">
        <div class="searchs">
            <div class="logo">
                <a href="/">
                    <img src="/img/web/simple_logo.png" alt="logo">
                </a>
            </div>
            <input id="addr-search-box" type="text" placeholder="도로명, 건물명 또는 지번으로 검색" inputmode="none"/>
            {{-- <button>
                <i class="fas fa-times"></i>
            </button> --}}
            <div class="search-list">
                    <ul class="historys">
                        {{-- <li class="subject">최근 검색기록</li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                            <div class="del">
                                <a><i class="fas fa-times"></i></a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                            <div class="del">
                                <a><i class="fas fa-times"></i></a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                            <div class="del">
                                <a><i class="fas fa-times"></i></a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                            <div class="del">
                                <a><i class="fas fa-times"></i></a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                            <div class="del">
                                <a><i class="fas fa-times"></i></a>
                            </div>
                        </li> --}}
                    </ul>
                    <ul class="places">

                        {{-- <li>
                            <div>
                                <span>역삼동 <b>678</b>-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <strong>TTTT</strong>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <strong>TTTT</strong>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <strong>TTTT</strong>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <strong>TTTT</strong>
                                <span>역삼동 678-23번지(역삼로 678길 999로)</span>
                            </div>
                        </li> --}}
                    </ul>
            </div>
        </div>
        <div class="links">

        </div>
        <div class="btns">
            <ul>
                <li>
                    <a href="javascript:;">
                        구독서비스
                    </a>
                </li>
                <li>
                    <a href="javascript:;">
                        고객센터
                    </a>
                </li>
                <li id="user-auth">
                    @guest('bduser')
                        <a id="hd-btn-login" href="javascript:;">
                            로그인
                        </a>
                    @endguest
                    @auth('bduser')
                        <a id="hd-btn-mypage" href="javascript:;">
                            마이페이지
                        </a>
                    @endauth

                </li>
            </ul>
        </div>
    </header>
    <div class="tilewarp">
        <div class="tilemap top">
            <ul class="mchange">
                <li class="pic">기본</li>
                <li>심플</li>
                <li>숨김</li>
                <li class="babge"><i class="fa-solid fa-location-pin"></i></li>
            </ul>
            <ul class="tile">
                <li class="pic">일반</li>
                <li>지적</li>
                <li>위성</li>
                <li>로드뷰</li>
                <li class="babge"><i class="fa-solid fa-map"></i></li>
            </ul>
            <ul class="measure">
                <li>거리</li>
                <li>면적</li>
                <li>반경</li>
                <li class="babge"><i class="fa-solid fa-ruler"></i></li>
            </ul>
        </div>

        <div class="tilemap bottom">
            <ul class="location">
                <li><i class="fa-regular fa-compass"></i></li>
            </ul>
            <ul class="zoom">
                <li><i class="fa-sharp fa-solid fa-plus"></i></li>
                <li><i class="fa-sharp fa-solid fa-minus"></i></li>
            </ul>
        </div>
    </div>
    {{-- 0414 mypage add --}}
    <div class="side parent">
        <div class="side child">
            <div class="side-warp child-title">
                <i id="side-back" class="fa-solid fa-arrow-right"></i>
                <span id="cc-title">나의 관심 목록</span>
            </div>
        </div>
        <div class="my info">
            <ul class="my-profile">
                <li class="membership" data-group="my-bind" data-key="authority">-</li>
                <li data-group="my-bind" data-key="email">-</li>
            </ul>
            <button class="my-grade" type="button">구독 하기</button>
        </div>

        <div class="my list">
            <ul class="my-btns">
                <a id="ir-list" class="page ir" href="javascript:;">
                    <li>
                        <span class="c-title">나의 관심 목록</span>
                        <label>
                            <span data-group="my-bind" data-key="interests">0</span>
                        </label>
                    </li>
                </a>
                <a id="im-list" class="page im" href="javascript:;">
                    <li>
                        <span class="c-title">나의 정보 수정</span>
                    </li>
                </a>
                <a href="/account/find">
                    <li>
                        {{-- <i class="fa-solid fa-key"></i> --}}
                        <span>비밀번호 변경</span>
                    </li>
                </a>
                <a class="nt" href="javascript:;">
                    <li>
                        {{-- <i class="fa-solid fa-gear"></i> --}}
                        <span>마케팅 정보 수신</span>
                        <label class="mk-toggle">
                            <input type="checkbox" data-group="my-bind" data-key="marketing"/>
                            <span class="slider round"></span>
                        </label>
                    </li>
                </a>
                <a href="/auth/logout">
                    <li>
                        {{-- <i class="fa-solid fa-power-off"></i> --}}
                        <span>로그 아웃</span>
                    </li>
                </a>
            </ul>
        </div>
        <div class="ver-info">
            <span>VER. 0.0.1</span>
        </div>
    </div>
    {{-- <div class="side child">
        <div class="side-warp child-title">
            <i id="side-back" class="fa-solid fa-arrow-right"></i>
            <span id="cc-title">나의 관심 목록</span>
        </div>
    </div> --}}
    {{-- 0414 mypage add --}}
    <div id="nmap"></div>
    {{-- <div class="ndot d">
        <div class="dcontainer">
            <div class="subject">매매</div>
            <div class="sold-price">
                <span>9,999억</span>
            </div>
            <div class="unit-price">
                <span>평 9,999천만</span>
            </div>
        </div>
    </div> --}}
    <div class="map-compare">
        <div class="compare-list">

        </div>
        <div class="compare-button">
            <button class="parent-warp"><i class="fa-solid fa-clipboard-list"></i></button>

            <button class="sub-btn select hidden"><i class="fa-solid fa-building-circle-check"></i></button>
            <button class="sub-btn down hidden">
                <i class="fa-solid fa-circle-down"></i>
                <span class="total-compare-cnt">0</span>
            </button>
            <button class="sub-btn re hidden"><i class="fa-solid fa-arrow-rotate-left"></i></button>
        </div>
    </div>


    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=90ce3bf07676a605ed535d7678565c6d&libraries=services,clusterer,drawing"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript" src="/js/lib/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/js/crel/auths/session.js?v=230330"></script>
    <script type="text/javascript" src="/js/crel/auths/mypage.js?v=230417"></script>
    <script type="text/javascript" src="/js/crel/index.js?v=221220"></script>
    <script type="text/javascript" src="/js/crel/header.js?v=221220"></script>
    <script type="text/javascript" src="/js/crel/custom.js?v=221208"></script>
    <script type="text/javascript" src="/js/crel/map.js?v=221220"></script>
    <script type="text/javascript" src="/js/crel/layer.js?v=221220"></script>
    <script type="text/javascript" src="/js/crel/report.js?v=221220"></script>
    <script type="text/javascript" src="/js/crel/chart.js?v=221220"></script>
    <script type="text/javascript" src="/js/crel/marker.js?v=221201"></script>
    <script type="text/javascript" src="/js/crel/tile.js?v=221205"></script>
    <script type="text/javascript" src="/js/crel/format.js?v=230110"></script>
    <script type="text/javascript" src="/js/crel/api.js?v=230116"></script>
    <script type="text/javascript" src="/js/crel/search.js?v=230518"></script>


    <script src="https://kit.fontawesome.com/9f05d39656.js" crossorigin="anonymous"></script>
</body>

</html>
@if(session('error'))
    <script>
        alert('{{ session('error') }}');
    </script>
@endif
