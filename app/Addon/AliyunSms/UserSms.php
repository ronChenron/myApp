<?php

namespace App\Addon\AliyunSms;

use App\Models\Order;
use Auth;
use Illuminate\Support\Facades\Cache;

/**
 * 用户相关短信通知及验证
 * Class UserSms
 * @package App\Addon\AliyunSms
 */
class UserSms extends SendSms
{

    /**
     * 发送短信验证码
     * @param $mobile
     * @return bool|mixed|\SimpleXMLElement
     */
    public function sendVerifyCode($mobile)
    {
        $code = (string)rand(100000, 999999);
        Cache::put($mobile, $code, 5);      //验证码缓存1分钟
        return $this->sendTo($mobile, 'SMS_63455007', '麦田筑家网', compact('code'));
    }

    /**
     * 验证短信验证码
     * @param $mobile
     * @param $code
     * @return bool
     */
    public static function verifyCode($mobile, $code)
    {
        if (Cache::has($mobile)) {
            return !!(Cache::pull($mobile) == $code);
        }else {
            return false;
        }
    }

    /**
     * 用户下单成功发送短信通知
     *
     * @param Order $order
     * @return bool|mixed|\SimpleXMLElement
     */
    public function sendOrderNotice(Order $order)
    {
        return $this->sendTo($order->user->phone, 'SMS_60365331', '一简租', ['out_trade_no' => $order->out_trade_no]);
    }
}