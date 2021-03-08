<?php
namespace Hyperf\Tencent\Common;

use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\ApplicationContext;

class HttpClient
{
    private $client;

    public function __construct()
    {
        $clientFactory = ApplicationContext::getContainer()->get(ClientFactory::class);
        $this->client = $clientFactory->create(['timeout' => 20]);
    }

    // 发送http请求
    public function send($host, $method, $uri, array $data = [], array $header = [])
    {
        $url = $host . $uri;
        $options = [];
        if (! empty($header)) {
            $options['headers'] = $header;
        }
        switch (strtolower($method)) {
            case 'get':
                if (! empty($data)) {
                    $options['query'] = $data;
                }
                $result = $this->client->get($url, $options)->getBody()->getContents();
                break;
            case 'post':
                if (! empty($data)) {
                    $options['json'] = $data;
                }
                $result = $this->client->post($url, $options)->getBody()->getContents();
                break;
            case 'delete':
                if (! empty($data)) {
                    $options['query'] = $data;
                }
                $result = $this->client->delete($url, $options)->getBody()->getContents();
                break;
            case 'put':
                if (! empty($data)) {
                    $options['json'] = $data;
                }
                $result = $this->client->put($url, $options)->getBody()->getContents();
                break;
            case 'patch':
                if (! empty($data)) {
                    $options['json'] = $data;
                }
                $result = $this->client->patch($url, $options)->getBody()->getContents();
                break;
            default:
                throw new \Exception('method error!');
        }

        return json_decode($result, true);
    }
}
