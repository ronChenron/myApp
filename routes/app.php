<?php

/*
|--------------------------------------------------------------------------
| APP Routes
|--------------------------------------------------------------------------
|
| APP相关请求处理
| 移动端（APP等）请求写在这里，已添加跨域中间件cors
| prefix = app
|
*/

use Illuminate\Http\Request;

/**
 * /app/test
 */
Route::get('/test',function () {
    return "app请求测试";
});

Route::post('/request/token/{guard?}', 'AuthController@getToken');

Route::post('/refresh/token/{guard?}', 'AuthController@refreshToken');

/**
 * 发送短信验证码
 */
Route::post('/send/sms', function (Request $request) {
    return (new \App\Addon\AliyunSms\UserSms())->sendVerifyCode($request->get('phone'));
});

/**
 * 需要登陆请求
 * 请求头请带有如下信息
 *
 * $accessToken
 *
 * 'headers' => [
 *      'Accept' => 'application/json',
 *      'Authorization' => 'Bearer '.$accessToken,
 *  ],
 */
Route::group(['middleware'=> ['auth:app']], function() {

    /**
     * 用户信息
     */
    Route::get('/user', function(Request $request) {

        //控制器中快速获取登陆用户信息
        // $request->user()
        //获取登陆用户对象信息
        return Auth::guard('app')->user();
    });
});

Route::get('/socialite/token', 'AuthController@socialiteToken');