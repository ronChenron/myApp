<?php

namespace App\Http\Controllers\App;

use App\Models\Oauth;
use App\Models\User;
use App\Rules\Phone;
use App\Rules\VerifyPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AuthController extends AppController
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
     * 注册用户
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        //手机号及验证码验证
        $request->validate([
            'phone' => ['required', new Phone()],
            'verify_phone' => ['required', new VerifyPhone()],
            'password'=> ['required', 'min:6', 'max:32'],
            'name' => ['required', 'max:20']
        ]);
        if($this->create(['name' => $request->get('name'), 'phone' => $request->get('phone'), 'password' => bcrypt($request->get('password'))])) {
            return $this->getToken($request);
        }else {
            return $this->failed("注册失败");
        }
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => $data['password']
        ]);
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

    /**
     * 第三方授权登陆
     *
     * @request
     *  {
     *      'oauth_type':'qq',  //第三方类型 qq/wechat/weibo
     *      'oauth_id':'dshakjfdknflkhasd14',     //第三方标识openid/uid
     *      'oauth_info':用户信息   //json
     *  }
     *
     * @param Request $request
     * @return mixed
     */
    public function socialiteToken(Request $request)
    {
        $credentials = $request->only('oauth_type', 'oauth_id', 'oauth_info');
        $result = $this->personalToken($credentials);
        if($result) {
            return $result;
        }else {
            return $this->notFond("请先绑定手机号");
        }
    }

    protected function personalToken($credentials) {
        $oauth = Oauth::where([
            ['oauth_type', '=', $credentials['oauth_type']],
            ['oauth_id', '=', $credentials['oauth_id']]
        ])->first();
        if($oauth&&$oauth->user) {
            $oauth->oauth_info = $credentials['oauth_info'];
            $oauth->save();
            return $oauth->user->createToken($oauth->oauth_type.'_'.$oauth->oauth_id);
        }else {
            return false;
        }
    }

    /**
     * 绑定手机号
     * {
     *      'phone':'18611394471',
     *      'verify_phone':'12456'
     * }
     *
     * @param Request $request
     */
    public function bindPhone(Request $request)
    {
        //手机号及验证码验证
        $request->validate([
            'phone' => ['required', new Phone()],
            'verify_phone' => ['required', new VerifyPhone()]
        ]);

        $phone = $request->get('phone');
        $user = User::where('phone', $phone)->first();
        if(!$user) {
            //不存在的用户则创建
            $user = User::create(compact('phone'));
        }
        $credentials = $request->only('oauth_type', 'oauth_id', 'oauth_info');
        $user->oauths()->create($credentials);
        return $user->createToken($credentials['oauth_type'].'_'.$credentials['oauth_id']);
    }
}
