<?php
namespace Meeting;

use Common\HttpClient;
use Hyperf\Utils\ApplicationContext;

// 腾讯会议
class Meeting
{
    private $appId = "";
    private $secretId = "";
    private $secretKey = "";
    private $defaultMethod = 'POST';

    /* @var HttpClient $client */
    private $client;

    public function __construct()
    {
        $this->client = ApplicationContext::getContainer()->get(HttpClient::class);
    }

    // 分发接口请求
    public function send($method, $url, array $params = [], array $header = [])
    {
        $header = array_merge($header, $this->getHeader($params));

        return $this->client->send($method, $url, $params, $header);
    }

    // 公共设置参数
    protected function getHeader(array $params = [])
    {
        $time = time();
        $nonce = rand(100000, 999999);
        $signature = $this->sign($time, $nonce, json_encode($params));

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
    protected function sign($time = '', $nonce = '', $params = null, $uri = '/v1/meetings')
    {
        $headerString = "X-TC-Key={$this->secretId}&X-TC-Nonce={$nonce}&X-TC-Timestamp={$time}";
        $httpString = "{$this->defaultMethod}\n{$headerString}\n{$uri}\n{$params}";
        return base64_encode(hash_hmac("sha256", $httpString, $this->secretKey));
    }
}
