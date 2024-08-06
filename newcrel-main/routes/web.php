<?php

Route::get('/',[
    'uses' => 'IndexController@index',
    'as' => 'index.index'
])->middleware('prevent-multiple-logins');

Route::group(['prefix' => 'account'], function() { // 사용자 회원가입
    // 비밀번호 찾기
    Route::get('find', [
        'uses' => 'AccountsController@findView',
        'as' => 'account.findview'
    ]);
    Route::post('find', [
        'uses' => 'AccountsController@findSend',
        'as' => 'account.findsend'
    ]);
    Route::get('reset', [
        'uses' => 'AccountsController@resetView',
        'as' => 'account.resetview'
    ]);
    Route::post('reset', [
        'uses' => 'AccountsController@reset',
        'as' => 'account.reset'
    ]);

    // 약관동의
    Route::get('terms', [
        'uses' => 'AccountsController@termsView',
        'as' => 'account.termsview'
    ]);
    Route::post('terms', [
        'uses' => 'AccountsController@termsSave',
        'as' => 'account.save'
    ]);
    Route::post('send', [
        'uses' => 'AccountsController@sendMail',
        'as' => 'account.send'
    ]);
    Route::post('authVerification', [
        'uses' => 'AccountsController@verification',
        'as' => 'account.verifi'
    ]);
    Route::get('sign', [
        'uses' => 'AccountsController@signView',
        'as' => 'account.signview'
    ]);
    Route::post('sign', [
        'uses' => 'AccountsController@signRegist',
        'as' => 'account.signregist'
    ]);
});

// auth check
// Route::group(['middleware' => function ($request, $next) {
//     if(!Auth::check()) {
//         return response()-> json([
//             'error'=> true,
//             'msg'=> 'error',
//             'url'=> '/'
//         ]);
//     }
//     return $next($request);

// }], function() {

Route::group(['middleware' => 'login-check', 'prefix' => 'u'], function() {
    Route::get('mypage', [
        'uses' => 'MypageController@read',
        'as' => 'my.read'
    ]);
    Route::get('details', [
        'uses' => 'MypageController@detailsGet',
        'as' => 'my.detail'
    ]);
    Route::post('details/modify', [
        'uses' => 'MypageController@modify',
        'as' => 'my.modify'
    ]);
    Route::get('interest', [
        'uses' => 'MypageController@interestGet',
        'as' => 'my.interest'
    ]);
    Route::post('interest/delete', [
        'uses' => 'MypageController@interestDelete',
        'as' => 'my.interestdelete'
    ]);
    Route::post('concent', [
        'uses' => 'MypageController@marketingConcent',
        'as' => 'my.mkconcent'
    ]);
});
// });



Route::group(['prefix' => 'auth'], function() { // 계정
    Route::post('login', [
        'uses' => 'SessionsController@login',
        'as' => 'session.login'
    ]);
    Route::get('logout', [
        'uses' => 'SessionsController@destroy',
        'as' => 'session.destroy'
    ]);
});

Route::group(['prefix' => 'n'], function() {
    Route::post('/bounds',[
        'uses' => 'IndexController@bounds',
        'as' => 'index.bounds'
    ]);

    Route::get('/report',[
        'uses' => 'ReportController@info',
        'as' => 'report.info'
    ])->middleware('login-check');
});

