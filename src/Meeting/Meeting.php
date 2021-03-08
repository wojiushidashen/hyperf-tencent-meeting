<?php
namespace Hyperf\Tencent\Meeting;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Tencent\Common\HttpClient;
use Hyperf\Utils\ApplicationContext;

// 腾讯会议
class Meeting
{
    private $appId = '';
    private $secretId = '';
    private $secretKey = '';
    private $config;

    /* @var HttpClient $client */
    private $client;

    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->config = $container->get(ConfigInterface::class);
        $this->appId = $this->config->get('tencent_meeting.appId');
        $this->secretId = $this->config->get('tencent_meeting.secretId');
        $this->secretKey = $this->config->get('tencent_meeting.secretKey');
        $this->client = ApplicationContext::getContainer()->get(HttpClient::class);
    }

    // 分发接口请求
    public function send($method, $url, array $params = [], array $header = [])
    {
        $header = array_merge($header, $this->getHeader($method, $params));

        return $this->client->send($method, $url, $params, $header);
    }

    // 公共设置参数
    protected function getHeader($method, array $params = [])
    {
        $time = time();
        $nonce = rand(100000, 999999);
        $signature = $this->sign($method, $time, $nonce, json_encode($params));

        return [
            "X-TC-Key:{$this->secretId}", // 接口权限获取
            "X-TC-Timestamp:{$time}",
            "X-TC-Nonce:{$nonce}",
            "AppId:{$this->appId}", // 接口权限获取
            "X-TC-Signature:{$signature}",
            "content-type:application/json"
        ];
    }

    // 获取签名
    protected function sign($method, $time = '', $nonce = '', $params = null, $uri = '/v1/meetings')
    {
        $headerString = "X-TC-Key={$this->secretId}&X-TC-Nonce={$nonce}&X-TC-Timestamp={$time}";
        $httpString = "{$method}\n{$headerString}\n{$uri}\n{$params}";
        return base64_encode(hash_hmac("sha256", $httpString, $this->secretKey));
    }
}
