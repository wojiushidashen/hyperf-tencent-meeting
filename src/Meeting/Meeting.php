<?php

declare(strict_types=1);

namespace Hyperf\Tencent\Meeting;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Tencent\Common\HttpClient;
use Hyperf\Utils\ApplicationContext;

// 腾讯会议
class Meeting
{
    private $appId = '';

    private $sdkId = '';

    private $secretId = '';

    private $secretKey = '';

    private $config;

    /* @var HttpClient $client */
    private $client;

    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->config = $container->get(ConfigInterface::class);
        $this->sdkId = $this->config->get('tencent_meeting.appId');
        $this->appId = $this->config->get('tencent_meeting.sdkId');
        $this->secretId = $this->config->get('tencent_meeting.secretId');
        $this->secretKey = $this->config->get('tencent_meeting.secretKey');
        $this->client = ApplicationContext::getContainer()->get(HttpClient::class);
    }

    // 分发接口请求
    public function send($host, $method, $uri, array $params = [], array $header = [])
    {
        $header = array_merge($header, $this->getHeader($method, $uri, $params));
        return $this->client->send($host, $method, $uri, $params, $header);
    }

    // 公共设置参数
    protected function getHeader($method, $uri, array $params = [])
    {
        $time = time();
        $nonce = rand(100000, 999999);
        $signature = $this->sign($method, $time, $nonce, json_encode($params), $uri);

        return [
            'X-TC-Key' => $this->secretId,
            'X-TC-Timestamp' => $time,
            'X-TC-Nonce' => $nonce,
            'AppId' => $this->appId,
            'SdkId' => $this->sdkId,
            'X-TC-Signature' => $signature,
            'content-type' => 'application/json',
        ];
    }

    // 获取签名
    protected function sign($method, $time = '', $nonce = '', $params = null, $uri = '/v1/meetings')
    {
        $sortHeaderParams = [
            'X-TC-Key' => $this->secretId,
            'X-TC-Timestamp' => $time,
            'X-TC-Nonce' => $nonce,
        ];
        ksort($sortHeaderParams);
        $headerString = '';
        foreach ($sortHeaderParams as $k => $v) {
            $headerString .= $k . '=' . $v . '&';
        }
        $headerString = substr($headerString, 0, strlen($headerString) - 1);

        $httpString = "{$method}\n{$headerString}\n{$uri}\n{$params}";

        return base64_encode(hash_hmac('sha256', $httpString, $this->secretKey));
    }
}
