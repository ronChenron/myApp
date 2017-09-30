<?php
namespace App\Addon\AliyunMns;


use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Requests\PublishMessageRequest;

class SendSms
{
    public function sendTo($mobile, $template, $sign_name, array $data)
    {
        $client =new Client(config('services.aliyun_mns.endPoint'), config('services.aliyun_mns.key'), config('services.aliyun_mns.secret'));
        $topic = $client->getTopicRef(config('services.aliyun_mns.topic'));
        $batchSmsAttributes = new BatchSmsAttributes($sign_name, $template);
        $batchSmsAttributes->addReceiver($mobile, $data);
        $messageAttributes = new MessageAttributes(array($batchSmsAttributes));
        $messageBody = "smsmessage";
        $request = new PublishMessageRequest($messageBody, $messageAttributes);
        try {
            $result = $topic->publishMessage($request);
            if($result->isSucceed()) {
                return [
                    'message' => '短信发送成功'
                ];
            }else {
                return response('短信发送失败', 401);
            }
        } catch (MnsException $e) {
            throw $e;
        }
    }
}