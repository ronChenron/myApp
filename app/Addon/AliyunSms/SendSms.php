<?php
namespace App\Addon\AliyunSms;

use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Requests\PublishMessageRequest;

class SendSms
{
    public function sendTo($mobile, $template, $sign_name, array $data)
    {
        $endPoint = "http://1569989557773627.mns.cn-hangzhou.aliyuncs.com/";
        $topic = "sms.topic-cn-hangzhou";
        $client =new Client($endPoint, config('services.aliyunsms.key'), config('services.aliyunsms.secret'));
        $topic = $client->getTopicRef($topic);
        $batchSmsAttributes = new BatchSmsAttributes($sign_name, $template);
        $batchSmsAttributes->addReceiver($mobile, $data);
        $messageAttributes = new MessageAttributes(array($batchSmsAttributes));
        $messageBody = "smsmessage";
        $request = new PublishMessageRequest($messageBody, $messageAttributes);
        try {
            return $topic->publishMessage($request);
        } catch (MnsException $e) {
            throw $e;
        }
    }
}