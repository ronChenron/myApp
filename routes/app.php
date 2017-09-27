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

Route::post('/send/sms', function (Request $request) {
    $sms = new \Overtrue\EasySms\EasySms(config('services.sms'));
    $code = (string)rand(100000, 999999);
    $mobile = $request->get('phone');
    \Illuminate\Support\Facades\Cache::put($mobile, $code, 2);      //验证码缓存2分钟
    return $sms->send($mobile, [
        'content'  => "您的短信验证码是{$code}，有效期是1分钟，请勿泄露。",
        'template' => 'SMS_50365020',
        'data' => compact('code')
    ]);
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