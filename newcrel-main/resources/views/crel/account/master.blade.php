<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>부동산도서관 - @yield('title')</title>
    <link rel="stylesheet" href="/css/common/common.css">
    <link rel="stylesheet" href="/css/crel/account/master.css">
    <link rel="stylesheet" href="/css/crel/account/terms.css">
    <link rel="stylesheet" href="/css/crel/account/sign.css">
    <link rel="stylesheet" href="/css/crel/layer.css" version=221130>

</head>
<body>
    <div class="container">
        <div class="content">
            <header>
                <div>
                    {{-- 이용약관 동의 후 가입 절차 진행 --}}
                    <a href="/">
                        <img src="../img/web/logo.png" alt="logo" width="200"/>
                    </a>
                </div>
            </header>
            <main>
                <div>
                    @yield('terms')
                    @yield('sign')
                    @yield('find')
                    {{-- @yield('reset') --}}
                </div>
            </main>
            <footer>
                <div>© 2018. 부동산도서관 inc. all rights reserved.</div>
            </footer>
        </div>
    </div>

    <script type="text/javascript" src="/js/lib/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="@yield('script')"></script>
    <script type="text/javascript" src="/js/crel/mail.js?v=230222"></script>
    <script type="text/javascript" src="/js/crel/custom.js?v=221208"></script>
    <script type="text/javascript" src="/js/crel/layer.js?v=221220"></script>
    <script src="https://kit.fontawesome.com/9f05d39656.js" crossorigin="anonymous"></script>
</body>
</html>
