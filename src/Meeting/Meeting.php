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
        $this->sdkId = $this->config->get('tencent_meeting.sdkId');
        $this->appId = $this->config->get('tencent_meeting.appId');
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

    // 获取签名
    protected function sign($method, $timestamp = '', $nonce = '', $params = null, $uri = '/v1/meetings')
    {
        if (in_array($method, ['GET', 'DELETE'])) {
            $path = '';
            foreach ($params as $k => $v) {
                $path .= $k . '=' . $v . '&';
            }
            if (count($params) > 0) {
                $path = substr($path, 0, strlen($path) - 1);
                $uri .= '?' . $path;
            }
            $body = '';
        } else {
            $body = json_encode($params);
        }
        $headerString = "X-TC-Key={$this->secretId}&X-TC-Nonce={$nonce}&X-TC-Timestamp={$timestamp}";
        $strToSign = "{$method}\n{$headerString}\n{$uri}\n{$body}";
        $hash = hash_hmac('sha256', $strToSign, $this->secretKey);
        return base64_encode($hash);
    }

    // 公共设置参数
    protected function getHeader($method, $uri, array $params = [])
    {
        $time = (string) time();
        $nonce = (string) rand(10000, 999999);
        $signature = $this->sign(strtoupper($method), $time, $nonce, $params, $uri);

        return [
            'X-TC-Key' => $this->secretId,
            'X-TC-Timestamp' => $time,
            'X-TC-Nonce' => $nonce,
            'X-TC-Signature' => $signature,
            'AppId' => $this->appId,
            'SdkId' => $this->sdkId,
            'URI' => $uri,
            'Content-Type' => 'application/json',
        ];
    }
}
