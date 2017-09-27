<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{

    /**
     * 获取token信息
     *
     * @request 请求参数
     * {
     *      "phone" => "18611394471",
     *      "password" => "secret"
     * }
     * @response    将结果在前端缓存
     * {
     *      "token_type": "Bearer",
     *      "expires_in": 1296000,      //过期时间（分）
     *      "access_token": "",         //token值
     *      "refresh_token": ""         //token过期后请求刷新令牌接口
     *  }
     *
     * @param Request $request
     * @param string $guard
     * @return mixed
     */
    public function getToken(Request $request, $guard = 'app')
    {
        return $this->authenticateClient($request, $guard);
    }
    
    
    //调用认证接口获取授权码
    protected function authenticateClient(Request $request, $guard)
    {
        $credentials = $request->only(config("auth.guards.{$guard}.username"), 'password');
        $request->request->add([
            'guard' => $guard,
            'grant_type' => 'password',
            'client_id' => config("auth.guards.{$guard}.client_id"),
            'client_secret' => config("auth.guards.{$guard}.client_secret"),
            'username' => $credentials[config("auth.guards.{$guard}.username")],
            'password' => $credentials['password'],
            'scope' => '',
        ]);
        $proxy = Request::create(
            'oauth/token',
            'POST'
        );
        $response = Route::dispatch($proxy);
        return $response;
    }

    /**
     * 刷新token
     *
     * @request 请求参数
     * {
     *      "refresh_token" => ""   //从前端缓存中读取
     * }
     * @response    将结果在前端缓存
     * {
     *      "token_type": "Bearer",
     *      "expires_in": 1296000,      //过期时间（分）
     *      "access_token": "",         //token值
     *      "refresh_token": ""         //token过期后请求刷新令牌接口
     *  }
     *
     * @param Request $request
     * @param string $guard
     * @return mixed
     */
    public function refreshToken(Request $request, $guard='app')
    {
        $request->request->add([
            'guard' => $guard,
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->get('refresh_token'),
            'client_id' => config("auth.guards.{$guard}.client_id"),
            'client_secret' => config("auth.guards.{$guard}.client_secret"),
            'scope' => '',
        ]);
        $proxy = Request::create(
            'oauth/token',
            'POST'
        );
        $response = Route::dispatch($proxy);
        return $response;
    }
}
